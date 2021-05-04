<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;
use AC\AC_DB;

class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;
		$appDir = dirname(__DIR__);

		$configurator->setDebugMode(false);
		$configurator->enableTracy($appDir . '/log');

		$configurator->setTimeZone($GLOBALS['timezone']);
		$configurator->setTempDirectory($appDir . '/temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->addDynamicParameters([
			'db_dsn' => AC_DB::get_db_dsn(),
			'db_user' => constant("DB_USER"),
			'db_password' => constant("DB_PASSWORD"),
			'db_prefix' => AC_DB::get_db_prefix(),
		]);

		$configurator->addConfig($appDir . '/config/common.neon');
		$configurator->addConfig($appDir . '/config/database.neon');

		return $configurator;
	}
}
