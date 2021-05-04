<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\SiteManager;
use App\Model\NavigationManager;

final class Error4xxPresenter extends Nette\Application\UI\Presenter
{

	private $db_prefix;
    private $database;
    private $siteManager;
    private $navigationManager;

    private $siteDetails;

    public function __construct($db_prefix, Nette\Database\Explorer $database, SiteManager $siteManager,  NavigationManager $navigationManager)
    {

        $this->db_prefix = $db_prefix;
        $this->database = $database;
        $this->siteManager = $siteManager;
        $this->navigationManager = $navigationManager;

    }

    public function beforeRender()
    {
        $this->template->siteDetails = $this->siteManager->siteDetails;

        $this->template->navigation = $this->navigationManager->getNavigation();

        $this->siteManager->siteDetails->maintenance ? $this->redirectPermanent('Homepage:default') : null;
    }

	public function startup(): void
	{
		parent::startup();
		if (!$this->getRequest()->isMethod(Nette\Application\Request::FORWARD)) {
			$this->error();
		}
	}


	public function renderDefault(Nette\Application\BadRequestException $exception): void
	{
        $this->template->title = $this->siteManager->getTitle(null);

        $this->setLayout(constant('ABSPATH') . $this->siteManager->siteDetails->theme . '/templates/@layout.latte');

        $location = constant('ABSPATH') . $this->siteManager->siteDetails->theme;
		$file = $location . "/templates/Error/{$exception->getCode()}.latte";
		$this->template->setFile(is_file($file) ? $file : $location . '/templates/Error/4xx.latte');
	}
}
