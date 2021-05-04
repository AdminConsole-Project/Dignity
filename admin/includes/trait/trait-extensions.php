<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    trait AC_Extensions_languages {

        function languages_admin_list(){

            $sql = "SELECT * FROM {$this->ac_db->db_prefix}languages WHERE type = :typ";

            $result = $this->ac_db->conn->prepare($sql);
            $result->execute(array(':typ' => 'admin'));

            if ($result-> rowCount() > 0){

                return $result->fetchAll();

            }
        }
    }

?>