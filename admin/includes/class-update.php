<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    class AC_Update{

        use AC_Latte;
        use AC_Latte_functions;
        use AC_Localization;

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
                $this->view_update();
            }
        }

        function view_contoller(){

            $this->latte_load();
            $this->latte_functions();

            switch ($_GET["view"]){

                case "update":
                    $this->view_update();
                break;
                default:
                    $this->view_update();
            }
        }

        function action_contoller(){

            switch ($_GET["action"]){

                case "login":
                    $this->action_login();
                break;

            }
        }

        function view_update(){

            $this->lang_load('core','update');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["update"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array(),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'update',
                'lang_interface' => $lang_interface,
                'lang' => $lang,
            );

            $this->latte_parameters['ac_version'] = $GLOBALS["ac_version"];

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/update.html', $this->latte_parameters);

        }
    }

?>