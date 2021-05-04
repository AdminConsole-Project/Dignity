<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    class AC_Media{

        use AC_Latte;
        use AC_Latte_functions;
        use AC_Localization;

        use AC_Media_list, AC_Media_upload_extra;

        private $ac_db;

        function __construct(){

            $this->ac_db = new AC_DB;

            $this->contoller();

        }

        function contoller(){

            if (isset($_GET["view"])){

                $this->view_contoller();

            }elseif (isset($_GET["action"])){

                $this->action_contoller();

            }else {
                $this->latte_load();
                $this->latte_functions();
                $this->view_list();
            }
        }

        function view_contoller(){

            $this->latte_load();
            $this->latte_functions();

            switch ($_GET["view"]){

                case "list":
                    $this->view_list();
                break;
                case "upload":
                    $this->view_upload();
                break;
                default:
                    $this->view_list();
            }
        }

        function action_contoller(){

            switch ($_GET["action"]){

                case "upload":
                    $this->action_upload();
                break;
                case "delete":
                    $this->action_delete();
                break;
            }
        }

        function view_list(){

            $this->lang_load('core','media');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["library"]["title"],
                'head_css_link' => array('css/interface.css','css/ac-upload.css'),
                'head_js_link' => array('../includes/js/dropzone/dropzone.js'),
                'footer_js_link' => array('js/pages/media-library.js', 'js/ac-dropzone.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'library',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters["media"] = $this->media_list();

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/media.html', $this->latte_parameters);

        }

        function view_upload(){

            $this->lang_load('core','media');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["upload"]["title"],
                'head_css_link' => array('css/interface.css', 'css/ac-upload.css'),
                'head_js_link' => array('../includes/js/dropzone/dropzone.js'),
                'footer_js_link' => array('js/ac-dropzone.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'upload',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/media.html', $this->latte_parameters);

        }

        function action_upload(){

            $month = date("m");
            $year = date("Y");

            $max_upload_size = ini_get('upload_max_filesize');
	        $max_upload_size = str_replace('M', '', $max_upload_size);
            $max_upload_size = $max_upload_size * 1024;

            if (isset($_FILES["file"]["name"])){

                $this->upload_folder();

                $uploadOk = 1;
                $file = $_FILES["file"]["name"];

                $file_name = Nette\Utils\Strings::webalize(pathinfo($file,PATHINFO_FILENAME));
                $file_type = strtolower(pathinfo($file,PATHINFO_EXTENSION));

                if ($_FILES["file"]["size"] > $max_upload_size){
                    $uploadOk = 0;
                }

                if ($file_type == "apng" || $file_type == "bmp" || $file_type == "gif" || $file_type == "ico" || $file_type == "cur" || $file_type == "jpg" || $file_type == "jpeg" || $file_type == "jfif" || $file_type == "pjpeg" || $file_type == "pjp" || $file_type == "png" || $file_type == "svg" || $file_type == "tif" || $file_type == "tiff") {

                    $target_directory = constant('ABSPATH').$this->upload_folder_sort("images");
                    $upload_type = "image";
                    $uploadOk = 1;

                }elseif ($file_type == "mp4" || $file_type == "webm" || $file_type == "ogg"){

                    $target_directory = constant('ABSPATH').$this->upload_folder_sort("video");
                    $upload_type = "video";
                    $uploadOk = 1;

                }elseif ($file_type == "mp3" || $file_type == "wav" || $file_type == "ogg"){

                    $target_directory = constant('ABSPATH').$this->upload_folder_sort("audio");
                    $upload_type = "audio";
                    $uploadOk = 1;

                }else {

                    $target_directory = constant('ABSPATH').'uploads/';
                    $uploadOk = 0;

                }

                $target_file_directory = $target_directory.$file_name.'.'.$file_type;

                if (file_exists($target_file_directory)) {

                    $file_new_name = $target_file_directory;

                    $i = 2;
                    while (file_exists($file_new_name)){

                        $file_new_name = $target_directory.$file_name.'-'.$i.'.'.$file_type;
                        $upload_name = $file_name.'-'.$i;
                        $upload_basename = $file_name.'-'.$i.'.'.$file_type;
                        $i++;
                    }

                    $target_file = $file_new_name;

                }else {

                    $upload_name = $file_name;
                    $upload_basename = $file_name.'.'.$file_type;
                    $target_file = $target_directory.$file_name.'.'.$file_type;

                }

                if ($uploadOk == 0){
                    echo 0;
                }else {

                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)){

                        $upload_location = str_replace(constant('ABSPATH'),"",$target_file);
                        $upload_size = $_FILES["file"]["size"];
                        $upload_mime = mime_content_type($target_file);

                        $data = array(
                            "name" => $upload_name,
                            "basename" => $upload_basename,
                            "location" => $upload_location,
                            "size" => $upload_size,
                            "type" => $upload_type,
                            "mime" => $upload_mime
                        );

                        $this->upload_database_insert($data);

                        echo 1;
                    }else {
                        echo 0;
                    }
                }
            }else {
                echo 0;
            }
        }

        function action_delete(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                if (!empty($_POST["id"])){

                    $id = $_POST["id"];

                    $sql = "SELECT location FROM {$this->ac_db->db_prefix}media WHERE ID = :id limit 1";

                    $result = $this->ac_db->conn->prepare($sql);

                    if ($result->execute(array( ':id' => $id))){

                        if ($result-> rowCount() > 0){

                            $row = $result->fetch();

                            $sql = "DELETE FROM {$this->ac_db->db_prefix}media WHERE ID = :id";

                            $result = $this->ac_db->conn->prepare($sql);

                            if ($result->execute(array( ':id' => $id))){

                                unlink(constant('ABSPATH').$row['location']);

                                echo 1;
                            }else {

                                echo 0;
                            }
                        }else {
                            echo 0;
                        }
                    }else {

                        echo 0;
                    }
                }else {
                    echo 0;
                }
            }else {
                echo 0;
            }
        }
    }

?>