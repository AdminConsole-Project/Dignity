<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    class AC_DB{

        public $conn;
        public $db_prefix;
        private $db_dsn;

        function __construct(){

            $this->db_connection();

        }

        function db_connection(){

            $this->db_dsn();

            $this->conn = new PDO($this->db_dsn, constant("DB_USER"), constant("DB_PASSWORD"));

            $this->db_prefix = $GLOBALS["table_prefix"];

        }

        function db_dsn(){

            $this->db_dsn = constant("DB_DRIVER").":host=".constant("DB_HOST").";dbname=".constant("DB_NAME").";charset=".constant("DB_CHARSET");

        }
    }

?>