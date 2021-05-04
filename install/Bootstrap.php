<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;


class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;

		$configurator->setDebugMode(false);
		$configurator->enableTracy(__DIR__ . '/log');

		$configurator->setTimeZone('Europe/Bratislava');
		$configurator->setTempDirectory(__DIR__ . '/temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__ . '/presenters')
			->addDirectory(__DIR__ . '/model')
			->register();

		$configurator->addConfig(__DIR__ . '/config/common.neon');

		return $configurator;
	}
}
