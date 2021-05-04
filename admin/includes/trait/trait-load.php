<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    trait AC_Load_controller {

        function contoller(){

            $this->ac_db = new AC_DB;

            if (isset($_GET["action"])){

                $this->action_contoller();

            }
        }

        function action_contoller(){

            switch ($_GET["action"]){
                case "logout":
                    $this->action_logout();
                    exit;
                break;
                case "relogin":
                    $this->action_relogin();
                    exit;
                break;
                case "session-timeout":
                    $this->action_session_timeout();
                    exit;
                break;
            }
        }
    }

    trait AC_Load_action {

        function action_logout(){

            session_name(constant("AC_SESSION_NAME"));
            session_start();

            $this->session_database_inactivity();
            session_unset();
            session_destroy();

            if (isset($_GET["type"])){

                switch ($_GET["type"]){
                    case "inactivity":

                        header("Location: login.php?msg=inactivity");
                        exit;

                    break;
                }
            }else {

                header("Location: login.php?msg=logout");
                exit;

            }
        }

        function action_relogin(){

            session_name(constant("AC_SESSION_NAME"));
            session_start();

            if ($_SERVER["REQUEST_METHOD"] == "POST"){

                if (isset($_REQUEST["password"]) && !empty($_REQUEST["password"])){

                    $password = $_REQUEST["password"];

                    $sql = "SELECT password FROM {$this->ac_db->db_prefix}users WHERE ID = :id LIMIT 1";

                    $result = $this->ac_db->conn->prepare($sql);
                    $result->execute(array(':id' => $_SESSION["AC-ADMIN-ID"]));

                    if ($result-> rowCount() > 0){

                        while($row = $result->fetch()){

                            if (password_verify($password, $row["password"])){

                                $_SESSION['AC-ADMIN-SESSION-TIMEOUT'] = time();

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
            }
            exit;
        }

        function action_session_timeout(){

            session_name(constant("AC_SESSION_NAME"));
            session_start();

            $time_limit = $this->session_length();

            if (!empty($_SESSION["AC-ADMIN-ID"])){

                $elapsed_time = time() - $_SESSION['AC-ADMIN-SESSION-TIMEOUT'];

                if ($elapsed_time > $time_limit){
                    echo 0;
                }else {
                    echo 1;
                }
            }
            exit;
        }
    }

    trait AC_Load_session_database {

        function session_database(){

            $session_admin_id = $_SESSION["AC-ADMIN-ID"];

            $sql = "SELECT session FROM {$this->ac_db->db_prefix}users WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $session_admin_id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    if ($row['session'] !== NULL || !empty($row['session'])){

                        $session = json_decode($row['session'], true);

                        $ip_address = $_SERVER["REMOTE_ADDR"];
                        $token = $_COOKIE["ac-login-token"];

                        if ($session[$ip_address][$token]["status"] !== "active"){

                            $this->action_logout();

                        }
                    }else {

                        $this->action_logout();

                    }
                }
            }
        }

        function session_database_inactivity(){

            $session_admin_id = $_SESSION["AC-ADMIN-ID"];

            $sql = "SELECT session FROM {$this->ac_db->db_prefix}users WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $session_admin_id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    if ($row['session'] !== NULL || !empty($row['session'])){

                        $session = json_decode($row['session'], true);

                        $ip_address = $_SERVER["REMOTE_ADDR"];
                        $token = $_COOKIE["ac-login-token"];
                        $activity = date("Y-m-d H:i:s");

                        $session[$ip_address][$token]["status"] = "inactive";
                        $session[$ip_address][$token]["activity"] = $activity;
                        $session = json_encode($session);

                        $sql = "UPDATE {$this->ac_db->db_prefix}users SET session = :sessio  WHERE ID = :id";

                        $result = $this->ac_db->conn->prepare($sql);
                        $result->execute(array(':id' => $session_admin_id,':sessio' => $session));

                    }
                }
            }
        }

        function session_database_preferences(){

            $session_admin_id = $_SESSION["AC-ADMIN-ID"];

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}users WHERE ID = :id";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $session_admin_id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    $user_preferences = json_decode($row['preferences'], true);
                    $user_info = array('ID' => $row["ID"], 'username' => $row["username"], 'firstname' => $row["firstname"], 'lastname' => $row["lastname"], 'preferences' => $user_preferences);

                    $_SESSION["AC-ADMIN-INFO"] = $user_info;

                }
            }
        }

        function session_database_activity(){

            $session_admin_id = $_SESSION["AC-ADMIN-ID"];

            $sql = "SELECT session FROM {$this->ac_db->db_prefix}users WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $session_admin_id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    if ($row['session'] !== NULL || !empty($row['session'])){

                        $session = json_decode($row['session'], true);

                        $ip_address = $_SERVER["REMOTE_ADDR"];
                        $token = $_COOKIE["ac-login-token"];
                        $activity = date("Y-m-d H:i:s");

                        $session[$ip_address][$token]["activity"] = $activity;
                        $session = json_encode($session);

                        $sql = "UPDATE {$this->ac_db->db_prefix}users SET session = :sessio  WHERE ID = :id";

                        $result = $this->ac_db->conn->prepare($sql);
                        $result->execute(array(':id' => $session_admin_id,':sessio' => $session));

                    }
                }
            }
        }

        function session_length(){

            $sql = "SELECT value FROM {$this->ac_db->db_prefix}settings WHERE name = :nam LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':nam' => 'session_length'));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    $session_length = $row["value"] * 60;
                    return $session_length;

                }
            }
        }
    }

    trait AC_Load_redirections {

        function index_page_redirect(){

            if (basename($_SERVER['PHP_SELF']) !== "index.php"){
                header("Location: index.php");
                exit;
            }
        }

        function login_page_redirect(){

           if (basename($_SERVER['PHP_SELF']) !== "login.php"){
                header("Location: login.php");
                exit;
            }
        }

        function index_page_session_redirect(){

            if (basename($_SERVER['PHP_SELF']) == "login.php"){
                header("Location: index.php");
                exit;
            }
        }
    }

?>