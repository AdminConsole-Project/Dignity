<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    if (file_exists(__DIR__."/ac-config.php")){

        require_once __DIR__.'/ac-config.php';

        require_once constant("ABSPATH").'includes/ac-version.php';

        if (phpversion() >= $GLOBALS["php_version"]["max"]){

            echo 'PHP lower than 8 is required but higher or equal than 7.1';
            exit;

        }

        if (phpversion() < $GLOBALS["php_version"]["min"]){

            echo 'PHP 7.1 or higher is required but lower than 8';
            exit;

        }

    }else {

        if (file_exists(__DIR__."/install/index.php")){

            header("Location: install/index.php");
            exit;

        }else {

            echo "Cannot run installation. Installation folder do not exist.";
            exit;

        }
    }

?>