<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    class AC_Article{

        use AC_Latte;
        use AC_Latte_functions;
        use AC_Localization;

        use AC_Article_extra;

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
                case "article-visibility":
                    $this->action_visibility();
                break;
            }
        }

        function view_list(){

            $this->lang_load('core','article');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["list"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/article-list.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'list',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters["articles"] = $this->article_table_list();

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/article.html', $this->latte_parameters);

        }

        function view_edit(){

            if (empty($_GET['id'])){

                header("Location: article.php");
            }

            $id = $_GET['id'];

            $this->lang_load('core','article');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["edit"]["title"],
                'head_css_link' => array('css/interface.css', 'css/editor.css'),
                'head_js_link' => array('../includes/js/editor/tinymce/tinymce.min.js', 'js/editor-setup.js'),
                'footer_js_link' => array('js/pages/article-edit.js', 'js/editor-media.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'edit',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters['article_data'] = $this->action_edit_article($id);
            $this->latte_parameters["media_list"] = $this->media_list();

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/article.html', $this->latte_parameters);

        }

        function view_add(){

            $this->lang_load('core','article');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["add"]["title"],
                'head_css_link' => array('css/interface.css', 'css/editor.css'),
                'head_js_link' => array('../includes/js/editor/tinymce/tinymce.min.js', 'js/editor-setup.js'),
                'footer_js_link' => array('js/pages/article-add.js', 'js/editor-media.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'add',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters["media_list"] = $this->media_list();

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/article.html', $this->latte_parameters);

        }

        function action_add(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                if (!empty($_REQUEST["title"]) && isset($_REQUEST["content"]) && isset($_REQUEST["visibility"]) && !empty($_REQUEST["publication"])){

                    $alias = Nette\Utils\Strings::webalize($_REQUEST["title"]);

                    $date = date("Y-m-d H:i:s");
                    $date_gmt = gmdate("Y-m-d H:i:s");

                    $alias_new = $this->check_alias($alias);

                    $visibility = $_REQUEST["visibility"];
                    $title = $_REQUEST["title"];
                    $content = $_REQUEST["content"];

                    $sql = "INSERT INTO {$this->ac_db->db_prefix}articles (status, title, alias, content, category, author, date, date_gmt) VALUES (:stat, :titl, :ali, :content, :category, :author, :date, :date_gmt)";

                    $result = $this->ac_db->conn->prepare($sql);

                    if ($result->execute(array( ':stat' => $_REQUEST["visibility"], ':titl' => $_REQUEST["title"], ':ali' => $alias_new, ':content' => $_REQUEST["content"], ':category' => $_REQUEST["category"], ':author' => $_SESSION["AC-ADMIN-ID"], ':date' => $date, ':date_gmt' => $date_gmt))){

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

        function action_edit(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                if (!empty($_REQUEST["title"]) && isset($_REQUEST["content"]) && isset($_REQUEST["visibility"]) && !empty($_REQUEST["publication"])){

                    $id = $_POST["id"];

                    $alias = Nette\Utils\Strings::webalize($_REQUEST["title"]);

                    $date = date("Y-m-d H:i:s");
                    $date_gmt = gmdate("Y-m-d H:i:s");

                    $alias_new = $alias;

                    if ($this->action_get_alias($id) !== $alias){

                        $alias_new = $this->check_alias($alias);
                    }

                    $visibility = $_REQUEST["visibility"];
                    $title = $_REQUEST["title"];
                    $content = $_REQUEST["content"];

                    $sql = "UPDATE {$this->ac_db->db_prefix}articles SET status = :stat, title = :titl, alias = :ali, content = :cont, category = :cate WHERE ID = :id";

                    $result = $this->ac_db->conn->prepare($sql);

                    if ($result->execute(array( ':id' => $id, ':stat' => $_REQUEST["visibility"], ':titl' => $_REQUEST["title"], ':ali' => $alias_new, ':cont' => $_REQUEST["content"], ':cate' => $_REQUEST["category"]))){

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

        function action_delete(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                if (!empty($_POST["id"])){

                    $id = $_POST["id"];

                    $sql = "DELETE FROM {$this->ac_db->db_prefix}articles WHERE ID = :id";

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

                    $sql = "UPDATE {$this->ac_db->db_prefix}articles SET status = :stat WHERE ID = :id";

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