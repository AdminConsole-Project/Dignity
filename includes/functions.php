<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    function htacess_exist(string $dir){

        if (!file_exists($dir.".htaccess")){

            copy($dir.'includes/htaccess-copy.txt', $dir.'.htaccess');

        }
    }

    function timezone_load(){

        require_once constant("ABSPATH").'includes/class-timezone.php';
        require_once constant("ABSPATH").'includes/class-db.php';

        $a = new AC_Timezone_return;
        return $a->timezone_load();
    }

?>