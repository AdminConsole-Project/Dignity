<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    trait AC_Theme_extra {

        function theme_table_list(){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}themes";

            $result = $this->ac_db->conn->query($sql);

            if ($result-> rowCount() > 0){

                return $result->fetchAll();

            }
        }

        function action_is_main_theme($id){

            $sql = "SELECT status FROM {$this->ac_db->db_prefix}themes WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    if($row['status'] === "1"){

                        return true;

                    }
                }
            }

        }

        function action_get_theme_name($id){

            $sql = "SELECT name FROM {$this->ac_db->db_prefix}themes WHERE ID = :id";
            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    return $row['name'];
                }
            }
        }

        function action_delete_theme($dir){

            $files = array_diff(scandir($dir), array('.', '..'));

            foreach ($files as $file){

                if (is_dir($dir.'/'.$file)){

                    $this->action_delete_theme($dir.'/'.$file);

                }else {

                    unlink($dir.'/'.$file);

                }
            }

            return rmdir($dir);
        }

        function process_theme(string $file){

            $zip = new ZipArchive();

            if ($zip->open($file) === true){

                $location = $zip->statIndex(0);
                $location = $location['name'];

                if ($zip->locateName($location.'configuration.json') !== false){

                    $data = $zip->getStream($location."configuration.json");

                    $content = '';

                    while (!feof($data)) {
                        $content .= fread($data, 2);
                    }

                    $data = json_decode($content ,true);

                    $location = rtrim($location, '/');

                    if ($location === $data['name']){

                        if ($this->check_theme_dir($data['name']) || $this->check_theme_db($data['name'])){

                            echo 4;
                            exit;
                        }

                        $this->theme_add($data);

                        if (!$zip->extractTo(constant('ABSPATH').'themes/')){

                            echo 0;
                            exit;
                        }
                    }else {

                        echo 2;
                    }
                }else {

                    echo 3;
                    exit;
                }
            }else {

                echo 0;
                exit;
            }
        }

        function theme_add(array $data){

            $date = date("Y-m-d H:i:s");
            $date_gmt = gmdate("Y-m-d H:i:s");

            $sql = "INSERT INTO {$this->ac_db->db_prefix}themes (title, name, description, date, date_gmt) VALUES (:titl, :nam, :desc, :date, :date_gmt)";
            $result = $this->ac_db->conn->prepare($sql);

            if ($result->execute(array(':titl' => $data['title'], ':nam' => $data['name'], ':desc' => $data['description'], ':date' => $date, ':date_gmt' => $date_gmt))){
            }else {
                echo 0;
                exit;
            }
        }

        function check_theme_db(string $name){

            $sql = "SELECT name FROM {$this->ac_db->db_prefix}themes name = :nam";
            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':nam' => $name));

            if ($result-> rowCount() > 0){

                return true;
            }else {

                return false;
            }
        }

        function check_theme_dir(string $name){

            if (is_dir(constant('ABSPATH').'themes/'.$name) || file_exists(constant('ABSPATH').'themes/'.$name)){

                return true;
            }else {

                return false;
            }
        }
    }

?>