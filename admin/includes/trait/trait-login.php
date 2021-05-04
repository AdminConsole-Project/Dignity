<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    trait AC_Login_login_extra {

        function action_login_user_cookie(){

            if(isset($_REQUEST['remember']) && $_REQUEST['remember'] == TRUE){

                setcookie("ac-username", $_REQUEST["username"], time() + (86400), "/");

            }else {

                setcookie("ac-username", "", time() - (86400 * 30), "/");

            }
        }

        function action_login_date($id){

            $timestamp = date('Y-m-d H:i:s');

            $sql = "UPDATE {$this->ac_db->db_prefix}users SET last_login_date = :ldate  WHERE ID = :id";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id, ':ldate' => $timestamp));

        }

        function action_login_session($id,$session){

            $activity = date("Y-m-d H:i:s");
            $ip_address = $_SERVER["REMOTE_ADDR"];
            $token = $_COOKIE["ac-login-token"];

            if ($session === NULL || empty($session)){

                $session_info = array(
                    $ip_address => array(
                        $token => array(
                            "status" => "active",
                            "activity" => $activity
                        )
                    )
                );

                $session_json = json_encode($session_info, JSON_UNESCAPED_UNICODE);
                $this->action_login_session_update($id,$session_json);

            }else {

                $session_json = json_decode($session, true);

                if (array_key_exists($ip_address, $session_json)){

                    if (array_key_exists($token, $session_json[$ip_address])){

                        $session_info = array(
                            "status" => "",
                            "activity" => ""

                        );

                        $session_json[$ip_address][$token] += $session_info;
                        $session_json[$ip_address][$token]["status"] = "active";
                        $session_json[$ip_address][$token]["activity"] = $activity;

                        $session_json = json_encode($session_json, JSON_UNESCAPED_UNICODE);
                        $this->action_login_session_update($id,$session_json);

                    }else {

                        $session_info = array(
                            $token => array(
                                "status" => "",
                                "activity" => ""
                            )
                        );

                        $session_json[$ip_address] += $session_info;
                        $session_json[$ip_address][$token]["status"] = "active";
                        $session_json[$ip_address][$token]["activity"] = $activity;

                        $session_json = json_encode($session_json, JSON_UNESCAPED_UNICODE);
                        $this->action_login_session_update($id,$session_json);

                    }
                }else {

                    $session_info = array(
                        $ip_address => array(
                            $_COOKIE["ac-login-token"] => array(
                                "status" => "",
                                "activity" => "",
                            )
                        )
                    );

                    $session_json += $session_info;
                    $session_json[$ip_address][$token]["status"] = "active";
                    $session_json[$ip_address][$token]["activity"] = $activity;

                    $session_json = json_encode($session_info, JSON_UNESCAPED_UNICODE);
                    $this->action_login_session_update($id,$session_json);
                }
            }
        }

        function action_login_session_update($id,$session_json){

            $sql = "UPDATE {$this->ac_db->db_prefix}users SET session = :sessio  WHERE ID = :id";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id, ':sessio' => $session_json));

        }
    }

?>