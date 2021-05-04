<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    trait AC_Category_extra {

        function action_check_alias($alias, $level, $parent, $id = null, $type = false){

            if ($type === true && $id != null){

                $sql = "SELECT * FROM {$this->ac_db->db_prefix}categories WHERE ID = :id";

                $result = $this->ac_db->conn->prepare($sql);
                $result->execute(array(':id' => $id));

                if ($result-> rowCount() > 0){

                    while($row = $result->fetch()){

                        if ($alias == $row['alias'] && $level == $row['level'] && $parent == $row['parent']){

                            return FALSE;
                        }else {

                            $sql = "SELECT alias FROM {$this->ac_db->db_prefix}categories WHERE alias = :ali AND level = :levl AND parent = :paren";

                            $result = $this->ac_db->conn->prepare($sql);
                            $result->execute(array(':ali' => $alias, ':levl' => $level, ':paren' => $parent));

                            if ($result-> rowCount() > 0){

                                return TRUE;
                            }else {

                                return FALSE;
                            }
                        }
                    }
                }else {

                    return FALSE;
                }
            }elseif ($type === false){

                $sql = "SELECT alias FROM {$this->ac_db->db_prefix}categories WHERE alias = :ali AND level = :levl AND parent = :paren";

                $result = $this->ac_db->conn->prepare($sql);
                $result->execute(array(':ali' => $alias, ':levl' => $level, ':paren' => $parent));

                if ($result-> rowCount() > 0){

                    return TRUE;
                }else {

                    return FALSE;
                }
            }
        }

        function action_get_path($alias, $parent){

            if ($parent != 0){

                $sql = "SELECT path FROM {$this->ac_db->db_prefix}categories WHERE ID = :id LIMIT 1";

                $result = $this->ac_db->conn->prepare($sql);
                $result->execute(array(':id' => $parent));

                if ($result-> rowCount() > 0){

                    while($row = $result->fetch()){

                        $path = $row['path'];

                    }
                }

                return $path."/".$alias;

            }elseif ($parent == 0){

                return $alias;
            }
        }

        function action_update_child_path($parent){

            $sql = "SELECT ID, alias FROM {$this->ac_db->db_prefix}categories WHERE parent = :paren";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':paren' => $parent));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    $id = $row['ID'];
                    $alias = $row['alias'];

                    if ($parent != 0){

                        $level = $this->action_add_level($parent)+1;

                    }

                    $path = $this->action_get_path($alias, $parent);

                    $sql2 = "UPDATE {$this->ac_db->db_prefix}categories SET path = :pat, level = :levl WHERE ID = :id";
                    $result2 = $this->ac_db->conn->prepare($sql2);
                    $result2->execute(array( ':id' => $id, ':pat' => $path, ':levl' => $level));

                    $this->action_update_child_path($id);
                }
            }
        }

        function action_visibility_child($parent, $visibility){

            $sql = "SELECT ID FROM {$this->ac_db->db_prefix}categories WHERE parent = :paren";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':paren' => $parent));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    $id = $row['ID'];

                    $sql2 = "UPDATE {$this->ac_db->db_prefix}categories SET status = :stat WHERE ID = :id";
                    $result2 = $this->ac_db->conn->prepare($sql2);
                    $result2->execute(array( ':id' => $id, ':stat' => $visibility));

                    $this->action_visibility_child($id, $visibility);
                }
            }
        }

        function action_add_level($parent){

            $sql = "SELECT level FROM {$this->ac_db->db_prefix}categories WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $parent));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    return $row['level'];

                }
            }
        }
    }

    trait AC_Category_list {

        private $category_list_data = array();

        function category_list(){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}categories";

            $result = $this->ac_db->conn->query($sql);

            if ($result-> rowCount() > 0){

                return $result->fetchAll();

            }else {

                return array();

            }
        }

        function action_get_status($id){

            $sql = "SELECT status FROM {$this->ac_db->db_prefix}categories WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    return $row['status'];
                }
            }
        }

        function category_table_list(array $elements, array $branch = array(), $parentId = 0){

            if (empty($branch)){
                $branch = array();

                foreach ($elements as $element){
                    $branch[$element["parent"]][$element["ID"]] = $element;
                }
            }

            if (isset($branch[$parentId])){

                foreach ($branch[$parentId] as $row){

                    $this->category_list_data[] = array(
                        'ID' => $row['ID'],
                        'status' => $row['status'],
                        'level' => $row['level'],
                        'name' => $row['name'],
                        'alias' => $row['alias'],
                        'description' => $row['description'],
                        'level-mark' => $this->action_level_mark($row['level'])
                    );

                    $this->category_table_list($elements, $branch, $row["ID"]);

                }
            }

            return $this->category_list_data;
        }

        function action_level_mark(int $level){

            if ($level === 1){

                return null;

            }else {

                $level = $level-1;
                return str_repeat('-', $level);

            }
        }
    }

    trait AC_Category_edit {

        function action_edit_category($id){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}categories WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                return $result->fetch();
            }else {

                return 0;
            }
        }
    }

    trait AC_Category_delete {

        function action_delete_children(array $elements, array $branch = array(), $parentId){

            if (empty($branch)){
                $branch = array();

                foreach ($elements as $element){
                    $branch[$element["parent"]][$element["ID"]] = $element;
                }
            }

            if (isset($branch[$parentId])){

                foreach ($branch[$parentId] as $row){

                    $sql = "DELETE FROM {$this->ac_db->db_prefix}categories WHERE ID = :id";

                    $result = $this->ac_db->conn->prepare($sql);
                    if($result->execute(array( ':id' => $row['ID']))){

                    }

                    $this->action_delete_children($elements, $branch, $row["ID"]);

                }
            }
        }
    }

?>