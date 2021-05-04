<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    class AC_Load {

        use AC_Load_controller, AC_Load_action, AC_Load_session_database, AC_Load_redirections;

        private $ac_db;

        function __construct(){

            $this->login_token();

        }

        function config_file(){

            if (!file_exists("../ac-config.php")){

                if (file_exists("../install/index.php")){

                    header("Location: ../install/index.php");
                    exit;

                }else {

                    echo "Cannot run installation. Installation folder do not exist.";
                    exit;

                }
            }
        }

        function session(){

            session_name(constant("AC_SESSION_NAME"));
            session_start();

            $this->session_status();
            $this->session_timeout();
            $this->session_check_status();
        }

        function session_status(){

            if (empty($_SESSION["AC-ADMIN-ID"])){

                session_unset();
                session_destroy();
                $this->login_page_redirect();

            }else {

                $this->session_database();

            }
        }

        function session_timeout(){

            $time_limit = $this->session_length();

            if (!empty($_SESSION["AC-ADMIN-ID"])){

                $elapsed_time = time() - $_SESSION['AC-ADMIN-SESSION-TIMEOUT'];

                if ($elapsed_time > $time_limit){

                    $this->session_database_inactivity();
                    session_unset();
                    session_destroy();
                    $this->login_page_redirect();

                }else {

                    $_SESSION['AC-ADMIN-SESSION-TIMEOUT'] = time();
                    $this->index_page_session_redirect();
                    $this->session_database_preferences();
                    $this->session_database_activity();

                }
            }
        }

        function session_check_status(){

            $sql = "SELECT ID, session FROM {$this->ac_db->db_prefix}users";

            $result = $this->ac_db->conn->query($sql);

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    if ($row['session'] !== NULL || !empty($row['session'])){

                        $id = $row['ID'];
                        $session = json_decode($row['session'], true);

                        foreach ($session as $ip_address => $data){

                            foreach ($data as $token => $data_2){

                                $status = $data_2['status'];
                                $activity = $data_2['activity'];

                                $date = strtotime($activity);
                                $date = time() - $date;

                                if ($status === 'active'){

                                    if ($date > 300){

                                        $session[$ip_address][$token]["status"] = "inactive";

                                    }
                                }
                            }
                        }

                        $session = json_encode($session);

                        $sql_2 = "UPDATE {$this->ac_db->db_prefix}users SET session = :sessio  WHERE ID = :id";

                        $result_2 = $this->ac_db->conn->prepare($sql_2);
                        $result_2->execute(array(':id' => $id,':sessio' => $session));

                    }
                }
            }
        }

        function login_token(){

            if (!isset($_COOKIE["ac-login-token"])){

                $rand = rand();
                $token = hash("sha256", $rand);

                setcookie("ac-login-token", $token, time() + (86400 * 30), "/");

            }elseif (isset($_COOKIE["ac-login-token"]) && empty($_COOKIE["ac-login-token"])){

                $rand = rand();
                $token = hash("sha256", $rand);

                setcookie("ac-login-token", $token, time() + (86400 * 30), "/");

            }
        }
    }

?>