<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    trait AC_Navigation_extra {

        function navigation_list(){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}navigation";

            $result = $this->ac_db->conn->query($sql);

            if ($result-> rowCount() > 0){

                return $result->fetchAll();

            }
        }

        function page_list(){

            $sql = "SELECT ID, title, main_page FROM {$this->ac_db->db_prefix}pages";

            $result = $this->ac_db->conn->query($sql);

            if ($result-> rowCount() > 0){

                return $result->fetchAll();

            }
        }

        function article_list(){

            $sql = "SELECT ID, title FROM {$this->ac_db->db_prefix}articles";

            $result = $this->ac_db->conn->query($sql);

            if ($result-> rowCount() > 0){

                return $result->fetchAll();

            }
        }

        function get_page_name($id){

            $sql = "SELECT title FROM {$this->ac_db->db_prefix}pages WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    return $row['title'];
                }
            }
        }

        function get_article_name($id){

            $sql = "SELECT title FROM {$this->ac_db->db_prefix}articles WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    return $row['title'];
                }
            }
        }

        function get_category_name($id){

            $sql = "SELECT name FROM {$this->ac_db->db_prefix}categories WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    return $row['name'];
                }
            }
        }

        function check_alias($alias, $id = null){

            if ($id != null){

                $sql = "SELECT * FROM {$this->ac_db->db_prefix}navigation WHERE ID = :id";

                $result = $this->ac_db->conn->prepare($sql);
                $result->execute(array(':id' => $id));

                if ($result-> rowCount() > 0){

                    while($row = $result->fetch()){

                        if ($alias == $row['alias']){

                            return false;
                        }else {

                            $sql = "SELECT alias FROM {$this->ac_db->db_prefix}navigation WHERE alias = :ali";

                            $result = $this->ac_db->conn->prepare($sql);
                            $result->execute(array(':ali' => $alias));

                            if ($result-> rowCount() > 0){

                                return true;
                            }else {

                                return false;
                            }
                        }
                    }
                }else {

                    return false;
                }
            }else {

                $sql = "SELECT alias FROM {$this->ac_db->db_prefix}navigation WHERE alias = :ali";

                $result = $this->ac_db->conn->prepare($sql);
                $result->execute(array(':ali' => $alias));

                if ($result-> rowCount() > 0){

                    return true;
                }else {

                    return false;
                }
            }
        }

        function action_get_status($id){

            $sql = "SELECT status FROM {$this->ac_db->db_prefix}navigation WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    return $row['status'];
                }
            }
        }

        function action_edit_navigation($id){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}navigation WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                return $result->fetch();
            }else {

                return 0;
            }
        }
    }

?>