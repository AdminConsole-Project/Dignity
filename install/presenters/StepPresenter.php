<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Utils\Random;
use App\Model\LocalizationManager;
use App\Model\CompatibilityChecker;
use App\Model\InstallationManager;

final class StepPresenter extends Nette\Application\UI\Presenter
{

    /** @persistent */
	public $lang = 'en-US';

    public function beforeRender()
    {
        $this->template->lang = LocalizationManager::loadLanguage();

        $this->template->stepLogger = $this->getSession('ac_install_step') ?? null;
    }

    public function renderDefault()
    {
        $this->template->stepData = $this->getSession('ac_install_data')->stepLanguage ?? null;
    }

    public function actionSystem()
    {
        $language = $this->getHttpRequest()->getQuery('lang');

        if ($this->getSession('ac_install_step')->stepSystemSkip){
            if (
                CompatibilityChecker::checkPhpVersion()
                && CompatibilityChecker::checkExtension('pdo')
                && CompatibilityChecker::checkExtension('pdo_mysql')
                && CompatibilityChecker::checkExtension('json')
                && CompatibilityChecker::checkExtension('zip')
                && CompatibilityChecker::checkExtension('mbstring')
                && CompatibilityChecker::checkExtension('fileinfo')
            ){
                unset($this->getSession('ac_install_step')->stepSystemSkip);
                $this->getSession('ac_install_step')->stepSystem = true;
                $this->redirectPermanent('Step:main', ['lang' => $language]);

            }else {

                unset($this->getSession('ac_install_step')->stepSystemSkip);
                $this->getSession('ac_install_step')->stepSystem = false;
            }
        }
    }

    public function renderSystem()
    {
        $this->template->stepData = $this->getSession('ac_install_data')->stepSystem ?? null;
    }

    public function renderMain()
    {
        $timezone = file_get_contents('data/timezones.json');
        $timezone = json_decode($timezone);
        $this->template->timezone = $timezone;
        $this->template->stepData = $this->getSession('ac_install_data')->stepMain ?? null;
    }

    public function renderDatabase()
    {
        $this->template->dbPrefix = Random::generate(5, '1-9a-z') . '_';
        $this->template->stepData = $this->getSession('ac_install_data')->stepDatabase ?? null;
    }

    public function renderInstallation()
    {
        $stepLogger = $this->getSession('ac_install_step') ?? null;

        if ($stepLogger->stepLanguage && $stepLogger->stepSystem && $stepLogger->stepMain && $stepLogger->stepDatabase){

            InstallationManager::$db = $this->getSession('ac_install_data')->stepDatabase;
            InstallationManager::$main = $this->getSession('ac_install_data')->stepMain;
            $this->getSession('ac_installation')->progress = InstallationManager::install();
            $this->getSession()->destroy();

        }else {

            $this->flashMessage(LocalizationManager::loadLanguage()->step->overview->error[0]);
            $this->redirectPermanent('Step:overview');

        }
    }

    public function actionDeleteFolder()
    {
        InstallationManager::deleteInstallFolder('../install');
        $this->redirectUrl('../');
    }

}
