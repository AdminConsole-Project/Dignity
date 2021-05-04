<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    require_once 'includes/trait/trait-load.php';
    require 'includes/class-load.php';

    $a = new AC_Load;
    $a->config_file();

    require '../ac-load.php';
    require_once 'ac-config.php';

    require_once 'includes/functions.php';

    require_once constant("ABSPATH").'includes/class-db.php';
    require_once 'includes/class-localization.php';

    default_timezone();

    $a->contoller();
    $a->session();

    require_once constant("ABSPATH").'includes/class-latte.php';
    require_once 'includes/trait/trait-latte.php';

    class_navigation();

?>