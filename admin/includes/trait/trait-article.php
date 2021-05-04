<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    trait AC_Article_extra {

        function article_table_list(){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}articles";

            $result = $this->ac_db->conn->query($sql);

            if ($result-> rowCount() > 0){

                return $result->fetchAll();

            }
        }

        function check_alias($name, $id = null){

            $old = $name;
            $i = 2;

            $new = $old;

            $db = $this->check_alias_database($id);

            while (in_array($new, $db)){

                $new = $old.'-'.$i;
                $i++;
            }

            return $new;
        }

        function check_alias_database(string $id = null){

            if ($id !== null){

                $sql = "SELECT alias FROM {$this->ac_db->db_prefix}articles ORDER BY alias ASC WHERE ID <> :id ";
                $result = $this->ac_db->conn->prepare($sql);
                $result->execute(array( ':id' => $id));

            }else {

                $sql = "SELECT alias FROM {$this->ac_db->db_prefix}articles ORDER BY alias ASC";
                $result = $this->ac_db->conn->query($sql);

            }

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    $data[] = $row['alias'];
                }
            }else {

                $data[] = array();
            }

            return $data;
        }

        function action_get_status($id){

            $sql = "SELECT status FROM {$this->ac_db->db_prefix}articles WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    return $row['status'];
                }
            }
        }

        function action_get_alias($id){

            $sql = "SELECT alias FROM {$this->ac_db->db_prefix}articles WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    return $row['alias'];
                }
            }
        }

        function action_edit_article($id){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}articles WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                return $result->fetch();
            }else {

                return 0;
            }
        }

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

?>