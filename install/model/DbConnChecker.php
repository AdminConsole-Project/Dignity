<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


namespace App\Model;

use Nette;
use Nette\Database\Connection;

class DbConnChecker
{
	use Nette\SmartObject;

    static public function buildDsn(string $dbDriver, string $dbHost, string $dbName)
    {
        return "$dbDriver:host=$dbHost;dbname=$dbName";
    }

    public function preCheckSimple(string $dsn, string $user = null, string $password = null)
    {
        $reponse = (object) [];
        $reponse->status = 'OK';

        try {

            new Nette\Database\Connection($dsn, $user, $password);

        }catch(Nette\Database\ConnectionException $e){

            $reponse->status = 'ERROR';
            $reponse->log = $e;
        }

        return $reponse;
    }

    public function checkSimple(string $dsn, string $user = null, string $password = null)
    {
        $reponse = (object) [];
        $reponse->status = 'OK';

        try {

            new Nette\Database\Connection($dsn, $user, $password);

        }catch(Nette\Database\ConnectionException $e){

            $reponse->status = 'ERROR';
            $reponse->log = $e;
        }

        return $reponse;
    }
}

?>