<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Latte;
use Nette\Application\Helpers;
use App\Model\SiteManager;
use App\Model\NavigationManager;

final class HomepagePresenter extends Nette\Application\UI\Presenter{

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

        if ($this->siteManager->siteDetails->maintenance){
            if ($this->getAction() === 'default'){

                $this->template->title = $this->siteManager->getTitle(null);

                $this->setView('maintenance');
                $this->template->setFile(constant('ABSPATH') . $this->siteManager->siteDetails->theme . '/templates/Homepage/maintenance.latte');
                $this->setLayout(false);
            }
        }
    }

    public function renderDefault()
    {
        $this->template->setFile(constant('ABSPATH') . $this->siteManager->siteDetails->theme . '/templates/Homepage/default.latte');
        $this->setLayout(constant('ABSPATH') . $this->siteManager->siteDetails->theme . '/templates/@layout.latte');

        $pageData = $this->database->table($this->db_prefix.'pages')
            ->where('main_page', 1)
            ->limit(1)
            ->fetch();

        $content = $this->siteManager->setContentPaths($pageData->content);

        $this->template->title = $this->siteManager->getTitle(null);
        $this->template->content = new Latte\Runtime\Html($content);
    }
}
