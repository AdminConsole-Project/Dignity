<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    trait AC_Media_list {

        function media_list(){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}media ORDER BY ID DESC";

            $result = $this->ac_db->conn->query($sql);

            if ($result-> rowCount() > 0){

                return $result->fetchAll();

            }else {

                return null;

            }
        }
    }

    trait AC_Media_upload_extra {

        function upload_folder(){

            if (!is_dir(constant('ABSPATH').'uploads') || !file_exists(constant('ABSPATH').'uploads')){

                mkdir(constant('ABSPATH').'uploads',0755);

            }
        }

        function upload_folder_sort($type){

            $month =date("m");
            $year = date("Y");

            $date_sort = $this->upload_folder_sort_database("date");
            $type_sort = $this->upload_folder_sort_database("type");

            if ($type_sort === true || $type_sort === TRUE){

                $this->upload_folder_type();

                if(!empty($type)){

                    switch ($type){
                        case "audio":

                            if ($date_sort === true || $date_sort === TRUE){

                                if (!is_dir(constant('ABSPATH').'uploads/audio/'.$year.'/'.$month) || !file_exists(constant('ABSPATH').'uploads/audio/'.$year.'/'.$month)){

                                    mkdir(constant('ABSPATH').'uploads/audio/'.$year.'/'.$month, 0755, true);

                                }

                                return 'uploads/audio/'.$year.'/'.$month.'/';

                            }elseif ($date_sort === false || $date_sort === FALSE){

                                if (!is_dir(constant('ABSPATH').'uploads/audio') || !file_exists(constant('ABSPATH').'uploads/audio')){

                                    mkdir(constant('ABSPATH').'uploads/audio', 0755, true);

                                }

                                return 'uploads/audio/';
                            }
                        break;
                        case "images":

                            if ($date_sort === true || $date_sort === TRUE){

                                if (!is_dir(constant('ABSPATH').'uploads/images/'.$year.'/'.$month) || !file_exists(constant('ABSPATH').'uploads/images/'.$year.'/'.$month)){

                                    mkdir(constant('ABSPATH').'uploads/images/'.$year.'/'.$month, 0755, true);

                                }

                                return 'uploads/images/'.$year.'/'.$month.'/';

                            }elseif ($date_sort === false || $date_sort === FALSE){

                                if (!is_dir(constant('ABSPATH').'uploads/images') || !file_exists(constant('ABSPATH').'uploads/images')){

                                    mkdir(constant('ABSPATH').'uploads/images', 0755, true);

                                }

                                return 'uploads/images/';
                            }
                        break;
                        case "video":

                            if ($date_sort === true || $date_sort === TRUE){

                                if (!is_dir(constant('ABSPATH').'uploads/video/'.$year.'/'.$month) || !file_exists(constant('ABSPATH').'uploads/video/'.$year.'/'.$month)){

                                    mkdir(constant('ABSPATH').'uploads/video/'.$year.'/'.$month, 0755, true);

                                }

                                return 'uploads/video/'.$year.'/'.$month.'/';

                            }elseif ($date_sort === false || $date_sort === FALSE){

                                if (!is_dir(constant('ABSPATH').'uploads/video') || !file_exists(constant('ABSPATH').'uploads/video')){

                                    mkdir(constant('ABSPATH').'uploads/video', 0755, true);

                                }

                                return 'uploads/video/';
                            }
                        break;
                    }
                }
            }elseif ($type_sort === false || $type_sort === FALSE){

                if ($date_sort === true || $date_sort === TRUE){

                    if (!is_dir(constant('ABSPATH').'uploads/'.$year.'/'.$month) || !file_exists(constant('ABSPATH').'uploads/'.$year.'/'.$month)){

                        mkdir(constant('ABSPATH').'uploads/'.$year.'/'.$month, 0755, true);

                    }

                    return 'uploads/'.$year.'/'.$month.'/';

                }elseif ($date_sort === false || $date_sort === FALSE){

                    return 'uploads/';

                }
            }
        }

        function upload_folder_type(){

            $directory = array("audio","images","video");

            foreach ($directory as $name) {

                if (!is_dir(constant('ABSPATH').'uploads/'.$name) || !file_exists(constant('ABSPATH').'uploads/'.$name)){

                    mkdir(constant('ABSPATH').'uploads/'.$name, 0755);

                }
            }
        }

        function upload_database_insert($data){

            $date = date("Y-m-d H:i:s");
            $date_gmt = gmdate("Y-m-d H:i:s");

            $sql = "INSERT INTO {$this->ac_db->db_prefix}media (name,basename,location,size,type,mime_type,date,date_gmt) VALUES (:nam,:basenam,:location,:size,:type,:mime,:date,:date_gmt)";

            $result = $this->ac_db->conn->prepare($sql);

            if ($result->execute(array( ':nam' => $data["name"], ':basenam' => $data["basename"], ':location' => $data["location"], ':size' => $data["size"], ':type' => $data["type"], ':mime' => $data["mime"], ':date' => $date, ':date_gmt' => $date_gmt))){
            }else {
                echo 0;
                exit;
            }
        }

        function upload_folder_sort_database($type){

            if(!empty($type)){

                switch ($type){
                    case "date":
                        $sort_type = "media_date_sort";
                    break;
                    case "type":
                        $sort_type = "media_type_sort";
                    break;
                }
            }

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}settings WHERE name = :nam LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':nam' => $sort_type));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    return filter_var($row['value'], FILTER_VALIDATE_BOOLEAN);

                }
            }
        }
    }

?>