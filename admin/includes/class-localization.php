<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    trait AC_Localization{

        private $lang_location;
        private $lang_decode;
        private $lang_type;
        private $lang_filename;

        private $lang_configuration;
        private $lang_code;

        private $lang_update_location;
        private $lang_update_decode;

        private $session_admin_id;
        private $session_admin_username;

        function lang_load($lang_type, $lang_filename){

            $this->lang_type = $lang_type;
            $this->lang_filename = $lang_filename;

            if (isset($_SESSION["AC-ADMIN-ID"])){

                $this->session_admin_id = $_SESSION["AC-ADMIN-ID"];

            }

            if (isset($_SESSION["AC-ADMIN-USERNAME"])){

                $this->session_admin_username = $_SESSION["AC-ADMIN-USERNAME"];

            }

            $this->lang_controller();

        }

        function lang_controller(){

            if ($this->lang_type == 'core' || $this->lang_type == 'CORE'){

                $this->lang_type = 'core-';
                $this->lang_filename = $this->lang_type.$this->lang_filename.'.json';

            }else {

                $this->lang_filename = $this->lang_type.$this->lang_filename.'.json';

            }

            if (isset($_SESSION["AC-ADMIN-ID"])){

                if (!empty($_SESSION["AC-ADMIN-INFO"]['preferences']['language'])){

                    $user_lang = $_SESSION["AC-ADMIN-INFO"];
                    $user_lang2 = $user_lang['preferences']['language'];
                    $this->user_lang($user_lang2);

                    if (isset($_GET["lang"]) && !empty($_GET["lang"])){

                        $this->url_lang();

                    }
                }else {

                    if (isset($_GET["lang"]) && !empty($_GET["lang"])){

                        $this->url_lang();

                    }else {

                        $this->lang_db();

                    }
                }
            }else {

                if (isset($_GET["lang"]) && !empty($_GET["lang"])){

                    $this->url_lang();

                }else {

                    $this->lang_db();

                }
            }
        }

        function url_lang(){

            $lang_code = $_GET["lang"];

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}languages WHERE lang_code = :code AND type = :typ LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':code' => $lang_code, ':typ' => 'admin'));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    $this->lang_configuration = constant('ADMIN_ABSPATH')."languages/{$row['lang_code']}/configuration.json";
                    $this->lang_code = json_decode(file_get_contents($this->lang_configuration), true);

                    $this->lang_location = constant('ADMIN_ABSPATH')."languages/{$row['lang_code']}/".$this->lang_filename;

                    $this->lang_decode = json_decode(file_get_contents($this->lang_location), true);

                }

            }else {

                $sql = "SELECT * FROM {$this->ac_db->db_prefix}languages WHERE status = :stat AND type = :typ LIMIT 1";

                $result = $this->ac_db->conn->prepare($sql);
                $result->execute(array( ':stat' => '1', ':typ' => 'admin'));

                if ($result-> rowCount() > 0){

                    while($row = $result->fetch()){

                        $this->lang_configuration = constant('ADMIN_ABSPATH')."languages/{$row['lang_code']}/configuration.json";
                        $this->lang_code = json_decode(file_get_contents($this->lang_configuration), true);

                        $this->lang_location = constant('ADMIN_ABSPATH')."languages/{$row['lang_code']}/".$this->lang_filename;

                        $this->lang_decode = json_decode(file_get_contents($this->lang_location), true);

                    }
                }
            }
        }

        function lang_db(){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}languages WHERE status = :stat AND type = :typ LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':stat' => '1', ':typ' => 'admin'));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    $this->lang_configuration = constant('ADMIN_ABSPATH')."languages/{$row['lang_code']}/configuration.json";
                    $this->lang_code = json_decode(file_get_contents($this->lang_configuration), true);

                    $this->lang_location = constant('ADMIN_ABSPATH')."languages/{$row['lang_code']}/".$this->lang_filename;

                    $this->lang_decode = json_decode(file_get_contents($this->lang_location), true);

                    //$this->lang_update();
                }
            }else {

                $this->lang_configuration = constant('ADMIN_ABSPATH').'languages/en-US/configuration.json';
                $this->lang_code = json_decode(file_get_contents($this->lang_configuration), true);

                $this->lang_location = constant('ADMIN_ABSPATH').'languages/en-US/'.$this->lang_filename;

                $this->lang_decode = json_decode(file_get_contents($this->lang_location), true);

            }
        }

        function user_lang($lang){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}languages WHERE lang_code = :code AND type = :typ LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':code' => $lang, ':typ' => 'admin'));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    $this->lang_configuration = constant('ADMIN_ABSPATH')."languages/{$row['lang_code']}/configuration.json";
                    $this->lang_code = json_decode(file_get_contents($this->lang_configuration), true);

                    $this->lang_location = constant('ADMIN_ABSPATH')."languages/{$row['lang_code']}/".$this->lang_filename;

                    $this->lang_decode = json_decode(file_get_contents($this->lang_location), true);

                }
            }else {

                $this->lang_db();

            }
        }
    }

?>