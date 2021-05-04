<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    require_once '../includes/ac-version.php';
    if (phpversion() >= $GLOBALS["php_version"]["max"]){

        $message = 'PHP lower than 8 is required but higher or equal than 7.2';
        require_once 'php-version-error.php';
        exit;

    }

    if (phpversion() < $GLOBALS["php_version"]["min"]){

        $message = 'PHP 7.2 or higher is required but lower than 8';
        require_once 'php-version-error.php';
        exit;

    }

    require '../includes/nette/vendor/autoload.php';
    require __DIR__ . '/Bootstrap.php';

    App\Bootstrap::boot()
        ->createContainer()
        ->getByType(Nette\Application\Application::class)
        ->run();

?>