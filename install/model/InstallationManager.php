<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


namespace App\Model;

use Nette;
use Nette\Utils\DateTime;
use Nette\Utils\Random;

class InstallationManager
{
	use Nette\SmartObject;

    static public $db;
    static public $main;
    static public $progressStatus;

    static public function init(object $db, object $main){

        self::$db = $db;
        self::$main = $main;
    }

    static private function buildDsn(string $dbDriver, string $dbHost, string $dbName)
    {
        return "$dbDriver:host=$dbHost;dbname=$dbName";
    }

	static private function loadDatabase()
	{
        $dsn = self::buildDsn(self::$db->driver, self::$db->host, self::$db->name);
        return new Nette\Database\Connection($dsn, self::$db->user, self::$db->password);
	}

    static private function loadDatabaseExplorer()
	{
        $storage = new Nette\Caching\Storages\FileStorage('temp/');
        $connection = self::loadDatabase();
        $structure = new Nette\Database\Structure($connection, $storage);
        $conventions = new Nette\Database\Conventions\DiscoveredConventions($structure);
        return new Nette\Database\Explorer($connection, $structure, $conventions, $storage);
	}

    static public function install()
    {
        self::$progressStatus = (object) [];

        self::createConfigurationFile();
        self::createDatabaseTables();
        self::fillDatabase();

        return self::$progressStatus;
    }

    static private function createConfigurationFile()
    {
        $configSample = file_get_contents('data/ac-config-sample.txt');

        $configSample = str_replace('db_driver_here', self::$db->driver, $configSample);
        $configSample = str_replace('db_host_here', self::$db->host, $configSample);
        $configSample = str_replace('db_user_here', self::$db->user, $configSample);
        $configSample = str_replace('db_password_here', self::$db->password, $configSample);
        $configSample = str_replace('db_name_here', self::$db->name, $configSample);
        $configSample = str_replace('db_prefix_here', self::$db->prefix, $configSample);
        $configSample = str_replace('session_unique_name_here', 'AC'.Random::generate(36), $configSample);

        $configFile = $configSample;
        file_put_contents('../ac-config.php', $configFile);

        self::$progressStatus->configurationFile = true;
    }

    static private function createDatabaseTables()
    {
        self::loadDatabase()->query(self::prepareTables());
        self::$progressStatus->databaseTables = true;
    }

    static private function fillDatabase()
    {
        $date = DateTime::from('now');
        $dateGmt = gmdate("Y-m-d H:i:s");

        $userID = self::loadDatabaseExplorer()->table(self::$db->prefix . 'users')
            ->insert(
                [
                    'status' => 1,
                    'role' =>  'super',
                    'username' => self::$main->user->username,
                    'password' => self::$main->user->password,
                    'email' => self::$main->user->email,
                    'date' => $date,
                    'date_gmt' => $dateGmt
                ]
            );
        $userID->ID;

        self::loadDatabaseExplorer()->table(self::$db->prefix . 'articles')
            ->insert(
                [
                    'status' => 1,
                    'title' => 'Sample article',
                    'alias' => 'sample-article',
                    'content' => '<p>Sample article content</p>',
                    'author' => $userID,
                    'date' => $date,
                    'date_gmt' => $dateGmt
                ]
            );

        self::loadDatabaseExplorer()->table(self::$db->prefix . 'pages')
            ->insert(
                [
                    'status' => 1,
                    'main_page' => 1,
                    'title' => 'Sample page',
                    'alias' => 'sample-page',
                    'content' => '<p>Sample page content</p>',
                    'date' => $date,
                    'date_gmt' => $dateGmt
                ]
            );

        self::loadDatabaseExplorer()->table(self::$db->prefix . 'languages')
            ->insert(
                [
                    [
                        'status' => 1,
                        'type' => 'admin',
                        'title' => 'English (United States)',
                        'title_native' => 'English (United States)',
                        'lang_code' => 'en-US',
                        'version' => 0.9,
                        'lang_update' => 0,
                        'date' => $date,
                        'date_gmt' => $dateGmt
                    ],
                    [
                        'status' => 0,
                        'type' => 'admin',
                        'title' => 'Slovak (Slovakia)',
                        'title_native' => 'Slovenčina (Slovenská republika)',
                        'lang_code' => 'sk-SK',
                        'version' => 0.9,
                        'lang_update' => 0,
                        'date' => $date,
                        'date_gmt' => $dateGmt
                    ]
                ]
            );

        self::loadDatabaseExplorer()->table(self::$db->prefix . 'themes')
            ->insert(
                [
                    [
                        'status' => 1,
                        'title' => 'Material',
                        'name' => 'material',
                        'description' => 'Material theme is the built-in theme for Dignity.',
                        'date' => $date,
                        'date_gmt' => $dateGmt
                    ],
                    [
                        'status' => 0,
                        'title' => 'Agency',
                        'name' => 'agency',
                        'description' => 'Agency theme is the built-in theme for Dignity.',
                        'date' => $date,
                        'date_gmt' => $dateGmt
                    ],
                    [
                        'status' => 0,
                        'title' => 'Freelancer',
                        'name' => 'freelancer',
                        'description' => 'Material theme is the built-in theme for Dignity.',
                        'date' => $date,
                        'date_gmt' => $dateGmt
                    ]
                ]
            );
        self::loadDatabaseExplorer()->table(self::$db->prefix . 'settings')
            ->where('name', 'sitename')
            ->update(
                [
                    'value' => self::$main->sitename
                ]
            );

        self::loadDatabaseExplorer()->table(self::$db->prefix . 'settings')
            ->where('name', 'description')
            ->update(
                [
                    'value' => self::$main->siteDescription
                ]
            );

        self::loadDatabaseExplorer()->table(self::$db->prefix . 'settings')
            ->where('name', 'timezone')
            ->update(
                [
                    'value' => self::$main->timezone
                ]
            );

        self::$progressStatus->fillDatabase = true;
    }

    static private function prepareTables()
    {
        $tables = file_get_contents('sql/mysql.sql');
        $tables = str_replace('#__', self::$db->prefix, $tables);
        return $tables;
    }

    static public function deleteInstallFolder($dir)
    {

        $files = array_diff(scandir($dir), array('.', '..'));

        foreach ($files as $file){

            if (is_dir($dir.'/'.$file)){

                self::deleteInstallFolder($dir.'/'.$file);

            }else {

                unlink($dir.'/'.$file);

            }
        }

        return rmdir($dir);
    }
}

?>