<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    class AC_Settings{

        use AC_Latte;
        use AC_Latte_functions;
        use AC_Localization;

        use AC_Settings_general;

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
                $this->view_general();
            }
        }

        function view_contoller(){

            $this->latte_load();
            $this->latte_functions();

            switch ($_GET["view"]){

                case "general":
                    $this->view_general();
                break;
                case "maintenance":
                    $this->view_maintenance();
                break;
                case "seo":
                    $this->view_seo();
                break;
                case "media":
                    $this->view_media();
                break;
                default:
                    $this->view_general();
            }
        }

        function action_contoller(){

            switch ($_GET["action"]){

                case "general":
                    $this->action_general();
                break;
                case "maintenance":
                    $this->action_maintenance();
                break;
                case "seo":
                    $this->action_seo();
                break;
                case "media":
                    $this->action_media();
                break;
            }
        }

        function view_general(){

            $this->lang_load('core','settings');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["general"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/settings-general.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'general',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters['settings_data']['language'] = $this->get_settings('site_language');
            $this->latte_parameters['settings_data']['timezone'] = $this->get_settings('timezone');
            $this->latte_parameters['settings_data']['site_languages'] = $this->site_languages();
            $this->latte_parameters['settings_data']['site_timezones'] = $this->site_timezones();

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/settings.html', $this->latte_parameters);

        }

        function view_maintenance(){

            $this->lang_load('core','settings');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["maintenance"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/settings-maintenance.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'maintenance',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters['maintenance_status'] = filter_var($this->get_settings('maintenance'), FILTER_VALIDATE_BOOLEAN);

            $this->latte->render(constant('ADMIN_INTERFACE').'pages/settings.html', $this->latte_parameters);

        }

        function view_seo(){

            $this->lang_load('core','settings');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["seo"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/settings-seo.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'seo',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters['settings_data']['sitename-title'] = $this->get_settings('sitename_title');
            $this->latte_parameters['settings_data']['sitename-title-location'] = $this->get_settings('sitename_title_location');
            $this->latte->render(constant('ADMIN_INTERFACE').'pages/settings.html', $this->latte_parameters);

        }

        function view_media(){

            $this->lang_load('core','settings');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["media"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/settings-media.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'media',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters['settings_data']['media_date_sort'] = filter_var($this->get_settings('media_date_sort'), FILTER_VALIDATE_BOOLEAN);
            $this->latte_parameters['settings_data']['media_type_sort'] = filter_var($this->get_settings('media_type_sort'), FILTER_VALIDATE_BOOLEAN);
            $this->latte->render(constant('ADMIN_INTERFACE').'pages/settings.html', $this->latte_parameters);

        }

        function action_general(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                $sitename = $_POST["sitename"];
                $description = $_POST["description"];
                $site_language = $_POST["language"];
                $timezone = $_POST["timezone"];
                $author = $_POST["author"];

                $session_length = $_POST["session-length"];

                if (isset($_POST["show-author"])){

                    $show_author = 'true';

                }else {

                    $show_author = 'false';

                }

                try {

                    $sql = "UPDATE {$this->ac_db->db_prefix}settings SET value = :valu WHERE name = :nam";
                    $result = $this->ac_db->conn->prepare($sql);
                    $result->execute(array( ':valu' => $sitename, ':nam' => 'sitename'));

                    $sql = "UPDATE {$this->ac_db->db_prefix}settings SET value = :valu WHERE name = :nam";
                    $result = $this->ac_db->conn->prepare($sql);
                    $result->execute(array( ':valu' => $description, ':nam' => 'description'));

                    $sql = "UPDATE {$this->ac_db->db_prefix}settings SET value = :valu WHERE name = :nam";
                    $result = $this->ac_db->conn->prepare($sql);
                    $result->execute(array( ':valu' => $site_language, ':nam' => 'site_language'));

                    $sql = "UPDATE {$this->ac_db->db_prefix}settings SET value = :valu WHERE name = :nam";
                    $result = $this->ac_db->conn->prepare($sql);
                    $result->execute(array( ':valu' => $timezone, ':nam' => 'timezone'));

                    $sql = "UPDATE {$this->ac_db->db_prefix}settings SET value = :valu WHERE name = :nam";
                    $result = $this->ac_db->conn->prepare($sql);
                    $result->execute(array( ':valu' => $author, ':nam' => 'author'));

                    $sql = "UPDATE {$this->ac_db->db_prefix}settings SET value = :valu WHERE name = :nam";
                    $result = $this->ac_db->conn->prepare($sql);
                    $result->execute(array( ':valu' => $show_author, ':nam' => 'show_author'));

                    $sql = "UPDATE {$this->ac_db->db_prefix}settings SET value = :valu WHERE name = :nam";
                    $result = $this->ac_db->conn->prepare($sql);
                    $result->execute(array( ':valu' => $session_length, ':nam' => 'session_length'));

                    echo 1;

                }catch (Exception $e){

                    echo 3;

                }
            }else {
                echo 0;
            }
        }

        function action_maintenance(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                if (isset($_POST["maintenance"])){

                    $maintenance = 'true';

                }else {

                    $maintenance = 'false';

                }

                $message = $_POST["maintenance-message"];

                $sql = "UPDATE {$this->ac_db->db_prefix}settings SET value = :val  WHERE name = :nam";
                $result = $this->ac_db->conn->prepare($sql);

                if ($result->execute(array(':nam' => 'maintenance', ':val' => $maintenance))){

                    $sql = "UPDATE {$this->ac_db->db_prefix}settings SET value = :val  WHERE name = :nam";
                    $result = $this->ac_db->conn->prepare($sql);

                    if ($result->execute(array(':nam' => 'maintenance_message', ':val' => $message))){

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

        function action_seo(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                $sitename = $_POST["sitename-title-location"];
                $robots = $_POST["robots"];

                try {

                    $sql = "UPDATE {$this->ac_db->db_prefix}settings SET value = :valu WHERE name = :nam";
                    $result = $this->ac_db->conn->prepare($sql);
                    $result->execute(array( ':valu' => $sitename, ':nam' => 'sitename_title_location'));

                    $sql = "UPDATE {$this->ac_db->db_prefix}settings SET value = :valu WHERE name = :nam";
                    $result = $this->ac_db->conn->prepare($sql);
                    $result->execute(array( ':valu' => $robots, ':nam' => 'robots'));

                    echo 1;

                }catch (Exception $e){

                    echo 3;

                }
            }else {
                echo 0;
            }
        }

        function action_media(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                if (isset($_POST["media-date-sort"])){

                    $media_sort = 'true';

                }else {

                    $media_sort = 'false';

                }

                if (isset($_POST["media-type-sort"])){

                    $media_type_sort = 'true';

                }else {

                    $media_type_sort = 'false';

                }

                try {

                    $sql = "UPDATE {$this->ac_db->db_prefix}settings SET value = :valu WHERE name = :nam";
                    $result = $this->ac_db->conn->prepare($sql);
                    $result->execute(array( ':valu' => $media_sort, ':nam' => 'media_date_sort'));

                    $sql = "UPDATE {$this->ac_db->db_prefix}settings SET value = :valu WHERE name = :nam";
                    $result = $this->ac_db->conn->prepare($sql);
                    $result->execute(array( ':valu' => $media_type_sort, ':nam' => 'media_type_sort'));

                    echo 1;

                }catch (Exception $e){

                    echo 3;

                }
            }else {
                echo 0;
            }
        }
    }

?>