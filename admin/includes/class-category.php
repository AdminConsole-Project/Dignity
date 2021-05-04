<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    class AC_Category{

        use AC_Latte;
        use AC_Latte_functions;
        use AC_Localization;

        use AC_Category_extra, AC_Category_list, AC_Category_edit, AC_Category_delete;

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
                case "edit":
                    $this->view_edit();
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
                case "edit":
                    $this->action_edit();
                break;
                case "delete":
                    $this->action_delete();
                break;
                case "category-visibility":
                    $this->action_visibility();
                break;
            }
        }

        function view_list(){

            $this->lang_load('core','category');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["list"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/category-list.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'list',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters["categories"] = $this->category_table_list($this->category_list());

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/category.html', $this->latte_parameters);

        }

        function view_edit(){

            if (empty($_GET['id'])){

                header("Location: category.php");
            }

            $id = $_GET['id'];

            if ($this->action_edit_category($id) === 0){

                header("Location: category.php");
            }

            $this->lang_load('core','category');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["edit"]["title"],
                'head_css_link' => array('css/interface.css','css/editor.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/category-edit.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'edit',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters['category_data'] = $this->action_edit_category($id);

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/category.html', $this->latte_parameters);

        }

        function view_add(){

            $this->lang_load('core','category');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["add"]["title"],
                'head_css_link' => array('css/interface.css', 'css/editor.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/category-add.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'add',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/category.html', $this->latte_parameters);
        }

        function action_add(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                if (!empty($_POST["name"]) && isset($_POST["description"]) && isset($_POST["visibility"]) && isset($_POST["parent"])){

                    $visibility = $_POST["visibility"];
                    $name = $_POST["name"];
                    $description = $_POST["description"];
                    $parent = $_POST["parent"];

                    $level = 1;

                    if ($parent != 0){

                        $level = $this->action_add_level($parent)+1;

                    }

                    $alias = Nette\Utils\Strings::webalize($name);

                    $date = date("Y-m-d H:i:s");
                    $date_gmt = gmdate("Y-m-d H:i:s");

                    $check_alias = $this->action_check_alias($alias, $level, $parent);

                    $path = $this->action_get_path($alias, $parent);

                    if ($check_alias === TRUE){

                        echo 4;

                    }elseif ($check_alias === FALSE){

                        $sql = "INSERT INTO {$this->ac_db->db_prefix}categories (status,level,parent,path,name,alias,description,date,date_gmt) VALUES (:stat,:levl,:paren,:pat,:nam,:ali,:descr,:date,:date_gmt)";

                        $result = $this->ac_db->conn->prepare($sql);

                        if ($result->execute(array( ':stat' => $visibility, ':levl' => $level, ':paren' => $parent, ':pat' => $path,':nam' => $name, ':ali' => $alias,':descr' => $description, ':date' => $date, ':date_gmt' => $date_gmt))){

                            echo 1;
                        }else {

                            echo 3;
                        }
                    }
                }else {
                    echo 2;
                }
            }else {
                echo 0;
            }
        }

        function action_edit(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                if (!empty($_POST["id"]) && !empty($_POST["name"]) && isset($_POST["description"]) && isset($_POST["visibility"]) && isset($_POST["parent"])){

                    $id = $_POST["id"];

                    $visibility = $_POST["visibility"];
                    $name = $_POST["name"];
                    $description = $_POST["description"];
                    $parent = $_POST["parent"];

                    $level = 1;

                    if ($parent != 0){

                        $level = $this->action_add_level($parent)+1;

                    }

                    $alias = Nette\Utils\Strings::webalize($name);

                    $check_alias = $this->action_check_alias($alias, $level, $parent, $id, true);

                    $path = $this->action_get_path($alias, $parent);

                    if ($check_alias === TRUE){

                        echo 4;

                    }elseif ($check_alias === FALSE){

                        $sql = "UPDATE {$this->ac_db->db_prefix}categories SET status = :stat, level = :levl, parent = :paren , path= :pat, name = :nam, alias = :ali, description = :descr WHERE ID = :id";

                        $result = $this->ac_db->conn->prepare($sql);

                        if ($result->execute(array( ':id' => $id, ':stat' => $visibility, ':levl' => $level, ':paren' => $parent, ':pat' => $path,':nam' => $name, ':ali' => $alias,':descr' => $description))){

                            $this->action_update_child_path($id);

                            echo 1;
                        }else {

                            echo 3;
                        }
                    }
                }else {
                    echo 2;
                }
            }else {
                echo 0;
            }
        }

        function action_delete(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                if (!empty($_POST["id"])){

                    $id = $_POST["id"];

                    $sql = "DELETE FROM {$this->ac_db->db_prefix}categories WHERE ID = :id";

                    $result = $this->ac_db->conn->prepare($sql);

                    if ($result->execute(array( ':id' => $id))){

                        $this->action_delete_children($this->category_list(), array(), $id);

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

        function action_visibility(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                if (!empty($_POST["id"])){

                    $id = $_POST["id"];

                    if ($this->action_get_status($id) == 1){

                        $visibility = 0;

                    }else {

                        $visibility = 1;

                    }

                    $sql = "UPDATE {$this->ac_db->db_prefix}categories SET status = :stat WHERE ID = :id";

                    $result = $this->ac_db->conn->prepare($sql);

                    if ($result->execute(array( ':id' => $id, ':stat' => $visibility))){

                        $this->action_visibility_child($id, $visibility);

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