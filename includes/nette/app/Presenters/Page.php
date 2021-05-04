<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Latte;
use App\Model\SiteManager;
use App\Model\NavigationManager;

final class PagePresenter extends Nette\Application\UI\Presenter{

    private $db_prefix;
    private $database;
    private $siteManager;
    private $navigationManager;

    private $siteDetails;

    public function __construct($db_prefix, Nette\Database\Explorer $database, SiteManager $siteManager, NavigationManager $navigationManager)
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

    public function renderDefault(string $path = null, int $id = null){

        $this->template->setFile(constant('ABSPATH') . $this->siteManager->siteDetails->theme . '/templates/Page/default.latte');
        $this->setLayout(constant('ABSPATH') . $this->siteManager->siteDetails->theme . '/templates/@layout.latte');

        $pageData = $this->database->table($this->db_prefix.'pages')
            ->where('alias', $path)
            ->limit(1)
            ->fetch();

        if ($pageData->main_page === 1){
            $this->redirectPermanent('Homepage:default');
        }

        if ($pageData->status === 0){
            $this->error();
        }

        $content = $this->siteManager->setContentPaths($pageData->content);

        $this->template->title = $this->siteManager->getTitle($pageData->title);
        $this->template->content = new Latte\Runtime\Html($content);
    }
}
