<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\Responses;
use Nette\Utils\Strings;
use Nette\Utils\Validators;
use Nette\Security\Passwords;
use App\Model\CompatibilityChecker;
use App\Model\DbConnChecker;

final class ProcessPresenter extends Nette\Application\UI\Presenter
{

    /** @persistent */
	public $lang = 'en-US';

    private $passwords;
    private $dbConnChecker;

	public function __construct(Passwords $passwords, DbConnChecker $dbConnChecker)
	{
		$this->passwords = $passwords;
        $this->dbConnChecker = $dbConnChecker;
	}

    protected function startup()
    {
        parent::startup();
        if (!$this->getHttpRequest()->isMethod('POST')){

            $language = $this->getHttpRequest()->getPost('language');
            $this->redirectPermanent('Step:default', ['lang' => $language]);

        }
    }


    public function actionDefault()
    {
        $reponse = (object) [];

        $language = $this->getHttpRequest()->getPost('language');
        $this->getSession('ac_install_step')->stepLanguage = true;
        $this->getSession('ac_install_data')->stepLanguage = (object) [
            'language' => $language
        ];

        $this->getSession('ac_install_step')->stepSystemSkip = true;
        $reponse->status = 'OK';
        $reponse->redirectUrl = $this->link('Step:system', ['lang' => $language]);
        $this->sendJson($reponse);
    }

    public function actionSystem()
    {
        $language = $this->getHttpRequest()->getQuery('lang');

        if (
            CompatibilityChecker::checkPhpVersion()
            && CompatibilityChecker::checkExtension('pdo')
            && CompatibilityChecker::checkExtension('pdo_mysql')
            && CompatibilityChecker::checkExtension('json')
            && CompatibilityChecker::checkExtension('zip')
            && CompatibilityChecker::checkExtension('mbstring')
            && CompatibilityChecker::checkExtension('fileinfo')
        ){

            $this->getSession('ac_install_step')->stepSystem = true;
            $this->redirect('Step:main', ['lang' => $language]);

        }else {

            $this->getSession('ac_install_step')->stepSystem = false;
            $this->redirect('Step:main', ['lang' => $language]);
        }
    }

    public function actionMain()
    {
        $reponse = (object) [];
        $reponse->status = 'OK';

        $language = $this->getHttpRequest()->getQuery('lang');
        $this->getSession('ac_install_step')->stepMain = true;

        $password = $this->getSession('ac_install_data')->stepMain->user->password ?? null;

        $username = $this->getHttpRequest()->getPost('username');
        $username = Strings::trim($username);
        $username = Strings::webalize($username, null, false);

        $email = $this->getHttpRequest()->getPost('email') ?? null;
        $email = Strings::lower($email);

        if (!Validators::isEmail($email)){
            $reponse->status = 'ERROR';
            $reponse->message[] = 'E-mail no pattern';
        }

        if (!$this->getHttpRequest()->getPost('savedPassword') || $this->getHttpRequest()->getPost('password') && $this->getHttpRequest()->getPost('passwordRepeated')){

            $password = null;
            $passwordMain = $this->getHttpRequest()->getPost('password');
            $passwordRepeated = $this->getHttpRequest()->getPost('passwordRepeated');

            if ($passwordMain === $passwordRepeated){
                $password = $this->passwords->hash($passwordMain);
            }else {
                $reponse->status = 'ERROR';
                $reponse->message[] = 'Passwords no match';
            }
        }

        $this->getSession('ac_install_data')->stepMain = (object) [
            'sitename' => Strings::trim($this->getHttpRequest()->getPost('sitename')) ?? null,
            'siteDescription' => $this->getHttpRequest()->getPost('siteDescription') ?: null,
            'user' => (object) [
                'username' => $username,
                'email' => $email,
                'password' => $password ?? null,

            ],
            'timezone' => ($this->getHttpRequest()->getPost('useBrowserTimezone')) ? $this->getHttpRequest()->getPost('browserTimezone') : $this->getHttpRequest()->getPost('timezone')
        ];
        $reponse->redirectUrl = $this->link('Step:database', ['lang' => $language]);

        if ($reponse->status === 'ERROR'){
            $this->getSession('ac_install_step')->stepMain = false;
        }

        $this->sendJson($reponse);
    }

    public function actionDatabase()
    {
        $reponse = (object) [];
        $reponse->status = 'OK';

        $language = $this->getHttpRequest()->getQuery('lang');
        $this->getSession('ac_install_step')->stepDatabase = true;

        $db = (object) [
            'driver' => $this->getHttpRequest()->getPost('dbDriver'),
            'host' => $this->getHttpRequest()->getPost('dbHost'),
            'name' => $this->getHttpRequest()->getPost('dbName'),
            'user' => $this->getHttpRequest()->getPost('dbUser') ?? null,
            'password' => $this->getHttpRequest()->getPost('dbPassword') ?? null,
            'prefix' => $this->getHttpRequest()->getPost('dbPrefix') ?? null
        ];

        $this->getSession('ac_install_data')->stepDatabase = $db;
        $reponse->redirectUrl = $this->link('Step:overview', ['lang' => $language]);

        $dsn = DbConnChecker::buildDsn($db->driver, $db->host, $db->name);
        $reponse->db = $this->dbConnChecker->checkSimple($dsn, $db->user, $db->password);

        if ($reponse->db->status === 'ERROR'){
            $reponse->status = 'ERROR';
            $reponse->message[] = 'Database connection failed';
            $this->getSession('ac_install_step')->stepDatabase = false;
        }

        $this->sendJson($reponse);
    }

    public function actionCheckDbConnection()
    {
        $db = (object) [
            'driver' => $this->getHttpRequest()->getPost('dbDriver'),
            'host' => $this->getHttpRequest()->getPost('dbHost'),
            'name' => $this->getHttpRequest()->getPost('dbName'),
            'user' => $this->getHttpRequest()->getPost('dbUser') ?? null,
            'password' => $this->getHttpRequest()->getPost('dbPassword') ?? null,
            'prefix' => $this->getHttpRequest()->getPost('dbPrefix') ?? null
        ];

        $dsn = DbConnChecker::buildDsn($db->driver, $db->host, $db->name);
        $reponse = $this->dbConnChecker->preCheckSimple($dsn, $db->user, $db->password);

        $this->sendJson($reponse);
    }

}
