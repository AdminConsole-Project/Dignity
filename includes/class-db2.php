<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    namespace AC;

    class AC_DB{

        public static $show_dsn;

        static function get_db_dsn(){

            self::$show_dsn  = constant("DB_DRIVER").":host=".constant("DB_HOST").";dbname=".constant("DB_NAME").";charset=".constant("DB_CHARSET");

            return self::$show_dsn;
        }

        static function get_db_prefix(){

            return $GLOBALS["table_prefix"];
        }
    }

?>