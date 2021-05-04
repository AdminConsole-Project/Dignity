<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    class AC_Dashboard{

        use AC_Latte;
        use AC_Latte_functions;
        use AC_Localization;

        use AC_Dashboard_extra;

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
                $this->view_dashboard();
            }
        }

        function view_contoller(){

            $this->latte_load();
            $this->latte_functions();

            switch ($_GET["view"]){

                case "dashboard":
                    $this->view_dashboard();
                break;
                case "cache":
                    $this->view_cache();
                break;
                default:
                    $this->view_dashboard();
            }
        }

        function action_contoller(){

            switch ($_GET["action"]){

                case "clear-cache":
                    $this->action_clear_cache();
                break;

            }
        }

        function view_dashboard(){

            $this->lang_load('core','dashboard');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["dashboard"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array(),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'dashboard',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/dashboard.html', $this->latte_parameters);

        }

        function view_cache(){

            $this->lang_load('core','dashboard');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["cache"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/dashboard-cache.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'cache',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/dashboard.html', $this->latte_parameters);

        }

        function action_clear_cache(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                $this->clear_cache(constant("ABSPATH").'includes/nette/temp');
                echo 1;

            }else {
                echo 0;
            }

        }
    }

?>