<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    class AC_Login{

        use AC_Latte;
        use AC_Latte_functions;
        use AC_Localization;

        use AC_Login_login_extra;

        private $ac_db;

        function __construct(){

            $this->ac_db = new AC_DB;

            $this->lang_load('core','login');

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
                $this->view_login();
            }
        }

        function view_contoller(){

            $this->latte_load();
            $this->latte_functions();

            switch ($_GET["view"]){

                case "login":
                    $this->view_login();
                break;
                default:
                    $this->view_login();
            }
        }

        function action_contoller(){

            switch ($_GET["action"]){

                case "login":
                    $this->action_login();
                break;

            }
        }

        function view_login(){

            $this->latte_parameters = array(
                'lang_code' => $this->lang_code,
                'title' => $this->lang_decode["login"]["title"],
                'head_css_link' => array('css/ac-login.css'),
                'head_js_link' => array(),
                'footer_js_link' => array(),
                'body_id' => null,
                'body_class' => array(),
                'block' => 'login',
                'lang' => $this->lang_decode
            );

            $this->latte->render(constant('ADMIN_INTERFACE').'login.html', $this->latte_parameters);

        }

        function action_login(){

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                if (isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && !empty($_REQUEST["username"]) && !empty($_REQUEST["password"])){

                    $username = $_REQUEST["username"];
                    $password = $_REQUEST["password"];

                    $sql = "SELECT * FROM {$this->ac_db->db_prefix}users WHERE (username LIKE :user OR email LIKE :user) LIMIT 1";

                    $result = $this->ac_db->conn->prepare($sql);
                    $result->execute(array(':user' => $username));

                    if ($result-> rowCount() > 0){

                        while($row = $result->fetch()){

                            if (password_verify($password, $row["password"])){

                                $this->action_login_user_cookie();
                                $this->action_login_session($row["ID"], $row["session"]);

                                $user_preferences = json_decode($row['preferences'], true);
                                $user_info = array('ID' => $row["ID"], 'username' => $row["username"], 'firstname' => $row["firstname"], 'lastname' => $row["lastname"], 'preferences' => $user_preferences);

                                session_name(constant("AC_SESSION_NAME"));
                                session_start();

                                $_SESSION["AC-ADMIN-ID"] = $row["ID"];
                                $_SESSION["AC-ADMIN-INFO"] = $user_info;
                                $_SESSION['AC-ADMIN-SESSION-TIMEOUT'] = time();

                                $this->action_login_date($row["ID"]);

                                echo 1;

                            }else {
                                echo 2;
                            }
                        }
                    }else {
                        echo 2;
                    }
                }else {
                    echo 2;
                }
            }else {
                echo 0;
            }
        }
    }

?>