<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


namespace App\Model;

use Nette;

class CompatibilityChecker
{
	use Nette\SmartObject;

	static public function checkPhpVersion()
	{
		if (phpversion() >= 7.2){

			return true;
		}
		return false;
	}

	static public function checkExtension(string $ext)
	{
		if (extension_loaded($ext)){

			return true;
		}
		return false;
	}
}

?>