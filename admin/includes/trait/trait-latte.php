<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    trait AC_Latte_functions{

        function latte_functions(){

            $this->latte->addFunction("upload_max_size", function($type = null){

                $max_upload_size = (int)(ini_get('upload_max_filesize'));
                $max_post_size = (int)(ini_get('post_max_size'));
                $memory_limit = (int)(ini_get('memory_limit'));

                $size = min($max_upload_size , $max_post_size , $memory_limit);

                switch ($type){
                    case "bytes":
                        $size = $size * 1024;
                    break;
                    case "text":
                        $size = $size;
                    break;
                    default:
                        $size = $size.'M';
                }

                return $size;
            });

            $this->latte->addFunction('get_settings', function($value){

                return $this->get_settings($value);

            });

            $this->latte->addFunction('category_list_option', function(int $id = 0, int $parent_id = 0){

                $sql = "SELECT * FROM {$this->ac_db->db_prefix}categories WHERE status = :stat";

                $result = $this->ac_db->conn->prepare($sql);
                $result->execute(array(':stat' => '1'));

                if ($result-> rowCount() > 0){

                    $category_list = $result->fetchAll();

                }else {

                    $category_list = array();

                }

                function category_tree(int $id = 0, int $parent_id = 0, array $data, array $list = array(), int $parent = 0, string $level_mark = ''){

                    if (empty($list)){

                        $list = array();

                        foreach ($data as $row){

                            $list[$row["parent"]][$row["ID"]] = $row;

                        }
                    }

                    $level = $level_mark;

                    if (isset($list[$parent])){

                        foreach ($list[$parent] as $row ){

                            if ($row['ID'] != $id){

                                if ($row['status'] !== 0){

                                    if ($row['level'] === 1){

                                        if ($row['ID'] != $parent_id){

                                            echo '<option value="'.$row['ID'].'">'.$row['name'].'</option>';

                                        }else {

                                            echo '<option value="'.$row['ID'].'" selected>'.$row['name'].'</option>';

                                        }
                                    }elseif ($row['level'] !== 1){

                                        if ($row['ID'] != $parent_id){

                                            echo '<option value="'.$row['ID'].'">'.$level.'&nbsp;'.$row['name'].'</option>';

                                        }else {

                                            echo '<option value="'.$row['ID'].'" selected>'.$level.'&nbsp;'.$row['name'].'</option>';

                                        }
                                    }

                                    category_tree($id, $parent_id, $data, $list, $row["ID"], $level.'-');
                                }
                            }
                        }
                    }
                }

                category_tree($id, $parent_id, $category_list);
            });
        }
    }

?>