<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    trait AC_Settings_general {

        function get_settings($name){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}settings WHERE name = :nam LIMIT 1";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':nam' => $name));

            if ($result-> rowCount() > 0){

                while($row = $result->fetch()){

                    return $row['value'];

                }
            }
        }

        function site_languages(){

            $location = constant("ADMIN_ABSPATH").'data/languages.json';

            return json_decode(file_get_contents($location), true);

        }

        function site_timezones(){

            $location = constant("ADMIN_ABSPATH").'data/timezones.json';

            return json_decode(file_get_contents($location), true);

        }
    }

?>