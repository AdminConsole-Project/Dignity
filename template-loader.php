<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    require constant("ABSPATH").'includes/nette/vendor/autoload.php';
    require constant("ABSPATH").'includes/nette/app/Bootstrap.php';

    require_once constant("ABSPATH").'includes/functions.php';
    require_once constant("ABSPATH").'includes/class-db2.php';

    htacess_exist(constant("ABSPATH"));

    $timezone = timezone_load();

    App\Bootstrap::boot()
        ->createContainer()
        ->getByType(Nette\Application\Application::class)
        ->run();

?>