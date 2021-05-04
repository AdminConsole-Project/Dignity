<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    class AC_Navigation{

        use AC_Latte;
        use AC_Latte_functions;
        use AC_Localization;

        use AC_Navigation_extra;

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
                case "navigation-visibility":
                    $this->action_visibility();
                break;
                case "get-name":
                    $this->action_get_name();
                break;
            }
        }

        function view_list(){

            $this->lang_load('core','navigation');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["list"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/navigation-list.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'list',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters["navigations"] = $this->navigation_list();

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/navigation.html', $this->latte_parameters);

        }

        function view_edit(){

            if (empty($_GET['id'])){

                header("Location: navigation.php");
            }

            $id = $_GET['id'];

            if ($this->action_edit_navigation($id) === 0){

                header("Location: navigation.php");
            }

            $this->lang_load('core','navigation');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["edit"]["title"],
                'head_css_link' => array('css/interface.css', 'css/editor.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/navigation-edit.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'edit',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters['navigation_data'] = $this->action_edit_navigation($id);
            $this->latte_parameters["page_list"] = $this->page_list();
            $this->latte_parameters["article_list"] = $this->article_list();

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/navigation.html', $this->latte_parameters);

        }

        function view_add(){

            $this->lang_load('core','navigation');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["add"]["title"],
                'head_css_link' => array('css/interface.css', 'css/editor.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/navigation-add.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'add',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters["page_list"] = $this->page_list();
            $this->latte_parameters["article_list"] = $this->article_list();

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/navigation.html', $this->latte_parameters);

        }

        function action_add(){
            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                $name = $_POST["name"];
                $visibility = $_POST["visibility"];
                $position = $_POST["position"];

                $menu_items = array();
                $menu = array(
                    'presenter' => $_POST["presenter"] ?? null,
                    'action' => $_POST["action"] ?? null,
                    'id' => $_POST["identificator"] ?? null,
                    'original' => $_POST["original"] ?? null,
                    'text' => $_POST["text"] ?? null,
                    'url' => $_POST["url"] ?? null
                );

                if (!empty($menu['text'])){

                    foreach ($menu['text'] as $key => $n){

                        $menu_items[] = array(
                            'presenter' => $menu["presenter"][$key] ?: null,
                            'action' => $menu["action"][$key] ?: null,
                            'id' => $menu["id"][$key] ?: null,
                            'original' => $menu["original"][$key] ?? null,
                            'text' => $menu["text"][$key] ?: null,
                            'url' => $menu["url"][$key] ?: null
                        );
                    }
                }

                $menu_items = json_encode($menu_items, JSON_UNESCAPED_UNICODE);

                $alias = Nette\Utils\Strings::webalize($name);

                $date = date("Y-m-d H:i:s");
                $date_gmt = gmdate("Y-m-d H:i:s");

                if ($this->check_alias($alias)){

                    echo 2;

                }else{

                    $sql = "INSERT INTO {$this->ac_db->db_prefix}navigation (status,position,name,alias,structure,date,date_gmt) VALUES (:stat,:pos,:nam,:ali,:strc,:date,:date_gmt)";
                    $result = $this->ac_db->conn->prepare($sql);

                    if ($result->execute(array(':stat' => $visibility, ':pos' => $position, ':nam' => $name, ':ali' => $alias,':strc' => $menu_items, ':date' => $date, ':date_gmt' => $date_gmt))){

                        echo 1;
                    }else {

                        echo 0;
                    }
                }
            }else {

                echo 0;
            }
        }

        function action_edit(){
            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                $id = $_POST["id"];

                $name = $_POST["name"];
                $visibility = $_POST["visibility"];
                $position = $_POST["position"];

                $menu_items = array();
                $menu = array(
                    'presenter' => $_POST["presenter"] ?? null,
                    'action' => $_POST["action"] ?? null,
                    'id' => $_POST["identificator"] ?? null,
                    'original' => $_POST["original"] ?? null,
                    'text' => $_POST["text"] ?? null,
                    'url' => $_POST["url"] ?? null
                );

                if (!empty($menu['text'])){

                    foreach ($menu['text'] as $key => $n){

                        $menu_items[] = array(
                            'presenter' => $menu["presenter"][$key] ?: null,
                            'action' => $menu["action"][$key] ?: null,
                            'id' => $menu["id"][$key] ?: null,
                            'original' => $menu["original"][$key] ?? null,
                            'text' => $menu["text"][$key] ?: null,
                            'url' => $menu["url"][$key] ?: null
                        );
                    }
                }

                $menu_items = json_encode($menu_items, JSON_UNESCAPED_UNICODE);

                $alias = Nette\Utils\Strings::webalize($name);

                $date = date("Y-m-d H:i:s");
                $date_gmt = gmdate("Y-m-d H:i:s");

                if ($this->check_alias($alias, $id)){

                    echo 2;

                }else{

                    $sql = "UPDATE {$this->ac_db->db_prefix}navigation SET status = :stat, position = :pos, name = :nam, alias = :ali, structure = :strc WHERE ID = :id";
                    $result = $this->ac_db->conn->prepare($sql);

                    if ($result->execute(array(':id' => $id, ':stat' => $visibility, ':pos' => $position, ':nam' => $name, ':ali' => $alias,':strc' => $menu_items))){

                        echo 1;
                    }else {

                        echo 0;
                    }
                }
            }else {

                echo 0;
            }
        }

        function action_get_name(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                if (!empty($_POST["id"]) && !empty($_POST["type"])){

                    $id = $_POST["id"];
                    $type = $_POST["type"];

                    switch ($type){

                        case "page":
                            echo $this->get_page_name($id);
                        break;
                        case "article":
                            echo $this->get_article_name($id);
                        break;
                        case "category":
                            echo $this->get_category_name($id);
                        break;
                    }
                }
            }
        }

        function action_delete(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                if (!empty($_POST["id"])){

                    $id = $_POST["id"];

                    $sql = "DELETE FROM {$this->ac_db->db_prefix}navigation WHERE ID = :id";

                    $result = $this->ac_db->conn->prepare($sql);

                    if ($result->execute(array( ':id' => $id))){

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

                    $sql = "UPDATE {$this->ac_db->db_prefix}navigation SET status = :stat WHERE ID = :id";

                    $result = $this->ac_db->conn->prepare($sql);

                    if ($result->execute(array( ':id' => $id, ':stat' => $visibility))){

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