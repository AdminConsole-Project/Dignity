<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    class AC_Timezone{

        public $timezone;
        protected $ac_db;

        function __construct(){

            $this->ac_db = new AC_DB;
            $this->timezone_load();

        }

        function timezone_load(){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}settings WHERE name = :nam";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':nam' => 'timezone'));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    date_default_timezone_set($row['value']);

                }
            }
        }
    }

    class AC_Timezone_return{

        public $timezone;
        protected $ac_db;

        function __construct(){

            $this->ac_db = new AC_DB;
            $this->timezone_load();

        }

        function timezone_load(){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}settings WHERE name = :nam";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':nam' => 'timezone'));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    return $row['value'];

                }
            }
        }
    }

?>