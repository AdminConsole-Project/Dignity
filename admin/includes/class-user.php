<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    class AC_User{

        use AC_Latte;
        use AC_Latte_functions;
        use AC_Localization;

        use AC_User_profile_extra;

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
                case "profile":
                    $this->view_profile();
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
                case "profile":
                    $this->action_profile();
                break;
                case "logout-from-all":
                    $this->action_from_all();
                break;
                case "login-history":
                    $this->action_login_history();
                break;
            }
        }

        function view_list(){

            $this->lang_load('core','user');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["list"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/user-list.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'list',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters["users"] = $this->user_list();
            $this->latte_parameters["active_user_id"] = $_SESSION["AC-ADMIN-INFO"]["ID"];
            $this->latte->render(constant('ADMIN_INTERFACE').'pages/user.html', $this->latte_parameters);

        }

        function view_edit(){

            if (empty($_GET['id'])){

                header("Location: user.php");

            }elseif ($_GET['id'] === '1'){

                header("Location: user.php");

            }

            $id = $_GET['id'];

            if ($this->action_edit_user($id) === 0){

                header("Location: user.php");

            }

            $this->lang_load('core','user');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["edit"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/user-edit.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'edit',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters['installed_languages'] = $this->user_languages();
            $this->latte_parameters['user_data'] = $this->user_data($id);
            $this->latte->render(constant('ADMIN_INTERFACE').'pages/user.html', $this->latte_parameters);

        }

        function view_add(){

            $this->lang_load('core','user');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["add"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/user-add.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'add',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters['installed_languages'] = $this->user_languages();
            $this->latte->render(constant('ADMIN_INTERFACE').'pages/user.html', $this->latte_parameters);

        }

        function view_profile(){

            $this->lang_load('core','user');
            $lang = $this->lang_decode;

            $this->lang_load('core','interface');
            $lang_interface = $this->lang_decode;

            $this->latte_parameters = array(
                'user_info' => $_SESSION["AC-ADMIN-INFO"],
                'lang_code' => $this->lang_code,
                'title' => $lang["profile"]["title"],
                'head_css_link' => array('css/interface.css'),
                'head_js_link' => array(),
                'footer_js_link' => array('js/pages/user-profile.js'),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'profile',
                'lang_interface' => $lang_interface,
                'lang' => $lang
            );

            $this->latte_parameters['installed_languages'] = $this->user_languages();
            $this->latte_parameters['user_data'] = $this->user_data($_SESSION["AC-ADMIN-INFO"]["ID"]);
            $this->latte_parameters['user_active_sessions'] = $this->user_active_sessions($_SESSION["AC-ADMIN-INFO"]["ID"]);
            $this->latte->render(constant('ADMIN_INTERFACE').'pages/user.html', $this->latte_parameters);

        }

        function action_add(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                $username = Nette\Utils\Strings::webalize($_POST["username"], null, false);
                $firstname = $_POST["firstname"];
                $lastname = $_POST["lastname"];
                $email = $_POST["email"];

                $pwd = $_POST["password"];
                $hash = password_hash($pwd, PASSWORD_DEFAULT);

                $language = $_POST["language"];

                $language_array = array(
                    'language' => $language
                );

                $date = date("Y-m-d H:i:s");
                $date_gmt = gmdate("Y-m-d H:i:s");

                $language_array = json_encode($language_array, JSON_UNESCAPED_UNICODE);

                if ($this->user_username_exist($username)){
                    echo 2;
                    exit;
                }

                if ($this->user_email_exist($email)){
                    echo 3;
                    exit;
                }

                $sql = "INSERT INTO {$this->ac_db->db_prefix}users (status,role,username,firstname,lastname,password,email,preferences,date,date_gmt) VALUES (:stat,:role,:username,:firstn,:lastn,:pwd,:email,:preferences,:date,:date_gmt)";
                $result = $this->ac_db->conn->prepare($sql);

                if ($result->execute(array(':stat' => 'active', 'role' => 'super', ':username' => $username, ':firstn' => $firstname, ':lastn' => $lastname, ':pwd' => $hash,':email' => $email, ':preferences' => $language_array, ':date' => $date, ':date_gmt' => $date_gmt))){

                    echo 1;

                }else {
                    echo 0;
                }
            }else {
                echo 0;
            }
        }

        function action_edit(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                $id = $_POST["id"];
                $firstname = $_POST["firstname"];
                $lastname = $_POST["lastname"];
                $email = $_POST["email"];

                $pwd = $_POST["password"] ?? null;

                $language = $_POST["language"];

                $language_array = array(
                    'language' => $language
                );

                $language_array = json_encode($language_array, JSON_UNESCAPED_UNICODE);


                $sql = "UPDATE {$this->ac_db->db_prefix}users SET firstname = :firstn, lastname = :lastn, email = :email, preferences = :preferences  WHERE ID = :id";
                $result = $this->ac_db->conn->prepare($sql);

                if ($result->execute(array(':id' => $id, ':firstn' => $firstname, ':lastn' => $lastname, ':email' => $email, ':preferences' => $language_array))){

                    $this->user_change_password2($pwd, $id);

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

                    $sql = "DELETE FROM {$this->ac_db->db_prefix}users WHERE ID = :id";

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

        function action_profile(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                $id = $_POST["id"];
                $firstname = $_POST["firstname"];
                $lastname = $_POST["lastname"];
                $email = $_POST["email"];

                $language = $_POST["language"];

                $language_array = array(
                    'language' => $language
                );

                $language_array = json_encode($language_array, JSON_UNESCAPED_UNICODE);


                $sql = "UPDATE {$this->ac_db->db_prefix}users SET firstname = :firstn, lastname = :lastn, email = :email, preferences = :preferences  WHERE ID = :id";
                $result = $this->ac_db->conn->prepare($sql);

                if ($result->execute(array(':id' => $id, ':firstn' => $firstname, ':lastn' => $lastname, ':email' => $email, ':preferences' => $language_array))){

                    $this->user_change_password($_POST["change-password"], $id, $_POST["old-password"], $_POST["new-password"], $_POST["repeate-password"]);

                }else {
                    echo 0;
                }
            }else {
                echo 0;
            }
        }

        function action_from_all(){

            $id = $_SESSION["AC-ADMIN-ID"];

            $user_token = $_COOKIE["ac-login-token"];

            $sql = "SELECT session FROM {$this->ac_db->db_prefix}users WHERE ID = :id";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    if ($row['session'] !== NULL || !empty($row['session'])){

                            $session = json_decode($row['session'], true);

                    }else {

                        echo 0;

                    }

                    $session_update = $session;

                    foreach ($session as $ip_address => $data){

                        $ip_address = $ip_address;

                        foreach ($data as $token => $data_2){

                            $token = $token;

                            $status = $data_2['status'];

                            if ($status === 'active' && $token !== $user_token){

                                $session_update[$ip_address][$token]["status"] = "inactive";

                            }
                        }
                    }

                    $session_update = json_encode($session_update);

                    $sql2 = "UPDATE {$this->ac_db->db_prefix}users SET session = :sessio  WHERE ID = :id";

                    $result2 = $this->ac_db->conn->prepare($sql2);

                    if ($result2->execute(array(':id' => $id, ':sessio' => $session_update))){

                        echo 1;

                    }else {

                        echo 0;

                    }
                }
            }
        }

        function action_login_history(){

            $id = $_GET['id'];

            $sql = "SELECT session FROM {$this->ac_db->db_prefix}users WHERE ID = :id";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    if ($row['session'] !== NULL || !empty($row['session'])){

                            $session = json_decode($row['session'], true);

                    }else {

                        $file_data = 'User have not any login history';

                    }

                    $file = "login-history.txt";

                    $file_data = '';

                    foreach ($session as $ip_address => $data){

                        $ip_address = $ip_address;

                        $file_data .= 'IP ADDRESS'.PHP_EOL.$ip_address.PHP_EOL.PHP_EOL;

                        foreach ($data as $token => $data_2){

                            $token = $token;

                            $status = $data_2['status'];
                            $activity = $data_2['activity'];

                            $file_data .= 'TOKEN:'.$token.PHP_EOL;
                            $file_data .= 'STATUS:'.$status.' '.'ACTIVITY:'.$activity.PHP_EOL.PHP_EOL;

                        }

                        $file_data .= PHP_EOL.PHP_EOL;

                    }

                    header("Content-Disposition: attachment; filename={$file}");
                    header("Content-Type: text/plain");

                    echo $file_data;
                    exit;
                }
            }
        }
    }

?>