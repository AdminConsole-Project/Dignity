<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


	/* AdminConsole database driver */
	define("DB_DRIVER", 'db_driver_here');

	/* AdminConsole database hostname */
	define("DB_HOST", 'db_host_here');

	/* AdminConsole database user */
	define("DB_USER", 'db_user_here');

	/* AdminConsole database password */
	define("DB_PASSWORD", 'db_password_here');

	/* AdminConsole database name */
	define("DB_NAME", 'db_name_here');

	/* AdminConsole database charset */
	define("DB_CHARSET", 'utf8mb4');

	/* AdminConsole database prefix */
	$table_prefix  = 'db_prefix_here';


	/* AdminConsole unigue session name */
	define("AC_SESSION_NAME", 'session_unique_name_here');


	/* AdminConsole debug mode */
	define("AC_DEBUG", FALSE);


	/* AdminConsole absolute path */
	if (!defined('ABSPATH')){

		define('ABSPATH', __DIR__.'/');

	}

?>