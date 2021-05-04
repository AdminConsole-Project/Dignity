<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    class AC_Theme{

        use AC_Latte;
        use AC_Latte_functions;
        use AC_Localization;

        use AC_Theme_extra;

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
                case "add":
                    $this->view_add();
                break;
                default:
                    $this->view_list();
            }
        }

        function action_contoller(){

            switch ($_GET["action"]){

                case "add":
                    $this->action_add();
                break;
                case "delete":
                    $this->action_delete();
                break;
                case "theme-main":
                    $this->action_main_theme();
                break;
            }
        }

        function view_list(){

            $this->lang_load('core','theme');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["list"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/theme-list.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'list',
                'lang_interface' => $lang_interface,
                'lang' => $lang,
            );

            $this->latte_parameters["themes"] = $this->theme_table_list();

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/theme.html', $this->latte_parameters);

        }

        function view_add(){

            $this->lang_load('core','theme');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["add"]["title"],
                'head_css_link' => array('css/interface.css', 'css/ac-upload.css'),
                'head_js_link' => array('../includes/js/dropzone/dropzone.js'),
                'footer_js_link' => array('js/pages/theme-add.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'add',
                'lang_interface' => $lang_interface,
                'lang' => $lang,
            );

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/theme.html', $this->latte_parameters);

        }

        function action_add(){

            $max_upload_size = ini_get('upload_max_filesize');
	        $max_upload_size = str_replace('M', '', $max_upload_size);
            $max_upload_size = $max_upload_size * 1024;

            if (isset($_FILES["file"]["name"])){

                $uploadOk = 1;
                $file = $_FILES["file"]["name"];

                $file_name = Nette\Utils\Strings::webalize(pathinfo($file,PATHINFO_FILENAME));
                $file_type = strtolower(pathinfo($file,PATHINFO_EXTENSION));

                if ($_FILES["file"]["size"] > $max_upload_size){

                    $uploadOk = 0;
                }

                if ($file_type == "zip"){

                    $target_directory = constant('ABSPATH').'themes/';
                    $uploadOk = 1;
                }else {

                    $target_directory = constant('ABSPATH').'themes/';
                    $uploadOk = 0;
                }

                $target_file = $target_directory.basename($_FILES["file"]["name"]);

                if ($uploadOk == 0){

                    echo 0;

                }else {

                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)){

                        $this->process_theme($target_file);

                        unlink($target_file);

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

                    if ($this->action_is_main_theme($id)){
                        echo 0;
                        exit;
                    }

                    $name = $this->action_get_theme_name($id);

                    $sql = "DELETE FROM {$this->ac_db->db_prefix}themes WHERE ID = :id AND status <> :stat";

                    $result = $this->ac_db->conn->prepare($sql);

                    if ($result->execute(array(':id' => $id, ':stat' => 1))){

                        $this->action_delete_theme(constant("ABSPATH")."themes/".$name);

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
        }

        function action_main_theme(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                $id = $_POST["id"];

                $sql = "UPDATE {$this->ac_db->db_prefix}themes SET status = :stat  WHERE status = :stati";
                $result = $this->ac_db->conn->prepare($sql);

                if ($result->execute(array(':stat' => '0',':stati' => '1'))){

                    $sql = "UPDATE {$this->ac_db->db_prefix}themes SET status = :stat  WHERE ID = :id";
                    $result = $this->ac_db->conn->prepare($sql);

                    if ($result->execute(array(':id' => $id,':stat' => '1'))){

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
        }
    }

?>