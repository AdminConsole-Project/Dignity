<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    class AC_Extensions{

        use AC_Latte;
        use AC_Latte_functions;
        use AC_Localization;

        use AC_Extensions_languages;

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
                $this->view_languages();
            }
        }

        function view_contoller(){

            $this->latte_load();
            $this->latte_functions();

            switch ($_GET["view"]){

                case "languages":
                    $this->view_languages();
                break;
                default:
                    $this->view_languages();
            }
        }

        function action_contoller(){

            switch ($_GET["action"]){

                case "language_default":
                    $this->action_language_default();
                break;

            }
        }

        function view_languages(){

            $this->lang_load('core','extensions');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["languages"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/extensions-languages.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'languages',
                'lang_interface' => $lang_interface,
                'lang' => $lang,
            );

            $this->latte_parameters["languages_admin"] = $this->languages_admin_list();

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/extensions.html', $this->latte_parameters);

        }

        function action_language_default(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                $id = $_POST["id"];

                $sql = "UPDATE {$this->ac_db->db_prefix}languages SET status = :stati  WHERE type = :typ AND status = :stata";
                $result = $this->ac_db->conn->prepare($sql);

                if ($result->execute(array(':typ' => 'admin',':stata' => '1',':stati' => '0'))){

                    $sql = "UPDATE {$this->ac_db->db_prefix}languages SET status = :stat  WHERE ID = :id AND type = :typ";
                    $result = $this->ac_db->conn->prepare($sql);

                    if ($result->execute(array(':id' => $id,':typ' => 'admin',':stat' => '1'))){

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