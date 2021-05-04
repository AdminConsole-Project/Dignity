<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


namespace App\Model;

use Nette;

class LocalizationManager
{
	use Nette\SmartObject;

	static public function loadLanguage()
	{
        $langCode = $_GET['lang'] ?? null;

        if ($langCode){

            if (file_exists('languages/'.$langCode.'.json')){

                $lang = file_get_contents('languages/'.$langCode.'.json');

            }else {

                $lang = file_get_contents('languages/en-US.json');

            }
        }else {

            $lang = file_get_contents('languages/en-US.json');
        }

        return json_decode($lang);
	}
}

?>