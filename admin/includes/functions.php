<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    function default_timezone(){

        require_once constant("ABSPATH").'includes/class-timezone.php';
        require_once constant("ABSPATH").'includes/class-db.php';

        $a = new AC_Timezone;

    }

    function class_login(){

        require_once constant('ADMIN_ABSPATH').'includes/trait/trait-login.php';
        require_once constant('ADMIN_ABSPATH').'includes/class-login.php';

        $a = new AC_Login;

    }

    function class_update(){

        require_once constant('ADMIN_ABSPATH').'includes/class-update.php';

        $a = new AC_Update;

    }

    function class_dashboard(){

        require_once constant('ADMIN_ABSPATH').'includes/trait/trait-dashboard.php';
        require_once constant('ADMIN_ABSPATH').'includes/class-dashboard.php';

        $a = new AC_Dashboard;

    }

    function class_article(){

        require_once constant('ADMIN_ABSPATH').'includes/trait/trait-article.php';
        require_once constant('ADMIN_ABSPATH').'includes/class-article.php';

        $a = new AC_Article;

    }

    function class_page(){

        require_once constant('ADMIN_ABSPATH').'includes/trait/trait-page.php';
        require_once constant('ADMIN_ABSPATH').'includes/class-page.php';

        $a = new AC_Page;

    }

    function class_category(){

        require_once constant('ADMIN_ABSPATH').'includes/trait/trait-category.php';
        require_once constant('ADMIN_ABSPATH').'includes/class-category.php';

        $a = new AC_Category;

    }

    function class_media(){

        require_once constant('ADMIN_ABSPATH').'includes/trait/trait-media.php';
        require_once constant('ADMIN_ABSPATH').'includes/class-media.php';

        $a = new AC_Media;

    }

    function class_navigation(){

        require_once constant('ADMIN_ABSPATH').'includes/trait/trait-navigation.php';
        require_once constant('ADMIN_ABSPATH').'includes/class-navigation.php';

        $a = new AC_Navigation;

    }

    function class_theme(){

        require_once constant('ADMIN_ABSPATH').'includes/trait/trait-theme.php';
        require_once constant('ADMIN_ABSPATH').'includes/class-theme.php';

        $a = new AC_Theme;

    }

    function class_extensions(){

        require_once constant('ADMIN_ABSPATH').'includes/trait/trait-extensions.php';
        require_once constant('ADMIN_ABSPATH').'includes/class-extensions.php';

        $a = new AC_Extensions;

    }

    function class_user(){

        require_once constant('ADMIN_ABSPATH').'includes/trait/trait-user.php';
        require_once constant('ADMIN_ABSPATH').'includes/class-user.php';

        $a = new AC_User;

    }

    function class_settings(){

        require_once constant('ADMIN_ABSPATH').'includes/trait/trait-settings.php';
        require_once constant('ADMIN_ABSPATH').'includes/class-settings.php';

        $a = new AC_Settings;

    }

    function class_library(){

        require_once constant('ADMIN_ABSPATH').'includes/class-library.php';

        $a = new AC_Library;

    }

?>