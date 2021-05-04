<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    trait AC_User_profile_extra {

        function action_edit_user($id){

            $sql = "SELECT username FROM {$this->ac_db->db_prefix}users WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                return 1;
            }else {

                return 0;
            }
        }

        function user_list(){

            $sql = "SELECT ID, username, firstname, lastname, email, last_login_date FROM {$this->ac_db->db_prefix}users";

            $result = $this->ac_db->conn->query($sql);

            if ($result-> rowCount() > 0){

                return $result->fetchAll();

            }else {

                return null;

            }
        }

        function user_data(int $id){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}users WHERE ID = :id LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    $user_preferences = json_decode($row['preferences'], true);
                    $user_sessions = json_decode($row['session'], true);

                    return array(
                        'ID' => $row["ID"],
                        'username' => $row["username"],
                        'firstname' => $row["firstname"],
                        'lastname' => $row["lastname"],
                        'email' => $row["email"],
                        'preferences' => $user_preferences,
                        'session' => $user_sessions
                    );

                }
            }
        }

        function user_languages(){

            $sql = "SELECT title_native, lang_code FROM {$this->ac_db->db_prefix}languages WHERE type = :typ ORDER BY title_native ASC";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':typ' => 'admin'));

            if ($result-> rowCount() > 0){

                return $result->fetchAll();

            }else {

                return null;

            }

        }

        function user_active_sessions(int $id){

            $ip_address = $_SERVER["REMOTE_ADDR"];
            $token = $_COOKIE["ac-login-token"];
            $sessions = array(
                'total' => null,
                'ip' => false,
                'browser' => false
            );

            $sql = "SELECT session FROM {$this->ac_db->db_prefix}users WHERE ID = :id";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':id' => $id));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    if ($row['session'] !== NULL || !empty($row['session'])){

                        $session_data = json_decode($row['session'], true);

                        foreach ($session_data as $session_ip => $session_data){

                            foreach ($session_data as $session_token => $session_data_2){

                                $status = $session_data_2['status'];

                                if ($ip_address !== $session_ip && $status === "active"){

                                    $sessions['total'] += 1;

                                }elseif ($ip_address === $session_ip && $token !== $session_token && $status === "active"){

                                    if ($sessions['total'] === null){

                                        $sessions['total'] += 1;
                                        $sessions['ip'] = true;

                                    }elseif ($sessions['total'] !== null){

                                        $sessions['total'] += 1;

                                    }
                                }elseif ($ip_address === $session_ip && $token === $session_token && $status === "active"){

                                    if ($sessions['ip'] === false && $sessions['total'] === null){

                                        $sessions['browser'] = true;

                                    }

                                    $sessions['total'] += 1;
                                }
                            }
                        }

                        return $sessions;
                    }
                }
            }
        }

        function user_change_password(string $change_pwd, int $id, string $old_pwd, string $new_pwd, string $repeate_pwd){

            if ($change_pwd === 'true'){

                $sql = "SELECT password FROM {$this->ac_db->db_prefix}users WHERE ID = :id LIMIT 1";

                $result = $this->ac_db->conn->prepare($sql);
                $result->execute(array(':id' => $id));

                if ($result-> rowCount() > 0){

                    while($row = $result->fetch()){

                        if (password_verify($old_pwd, $row["password"])){

                            if ($new_pwd === $repeate_pwd){

                                $hash = password_hash($new_pwd, PASSWORD_DEFAULT);

                                $sql2 = "UPDATE {$this->ac_db->db_prefix}users SET password = :pwd WHERE ID = :id";
                                $result2 = $this->ac_db->conn->prepare($sql2);

                                if ($result2->execute(array(':id' => $id, ':pwd' => $hash))){

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
                }else {
                    echo 0;
                }
            }else {
                echo 1;
            }
        }

        function user_change_password2(string $pwd, int $id){

            if ($pwd){

                $hash = password_hash($pwd, PASSWORD_DEFAULT);

                $sql2 = "UPDATE {$this->ac_db->db_prefix}users SET password = :pwd WHERE ID = :id";
                $result2 = $this->ac_db->conn->prepare($sql2);

                if ($result2->execute(array(':id' => $id, ':pwd' => $hash))){

                    echo 1;

                }else {
                    echo 0;
                }

            }else {
                echo 1;
            }
        }

        function user_username_exist(string $username){

            $sql = "SELECT username FROM {$this->ac_db->db_prefix}users WHERE username = :username LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':username' => $username));

            if ($result-> rowCount() > 0){

                return true;
            }
        }

        function user_email_exist(string $email){

            $sql = "SELECT email FROM {$this->ac_db->db_prefix}users WHERE email = :email LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':email' => $email));

            if ($result-> rowCount() > 0){

                return true;
            }
        }
    }

?>