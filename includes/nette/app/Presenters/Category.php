<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\SiteManager;
use App\Model\NavigationManager;
use App\Model\CategoryManager;

final class CategoryPresenter extends Nette\Application\UI\Presenter{

    private $db_prefix;
    private $database;
    private $siteManager;
    private $navigationManager;
    private $categoryManager;

    private $siteDetails;

    public function __construct($db_prefix, Nette\Database\Explorer $database, SiteManager $siteManager, NavigationManager $navigationManager, CategoryManager $categoryManager)
    {

        $this->db_prefix = $db_prefix;
        $this->database = $database;
        $this->siteManager = $siteManager;
        $this->navigationManager = $navigationManager;
        $this->categoryManager = $categoryManager;

    }

    public function beforeRender()
    {
        $this->template->siteDetails = $this->siteManager->siteDetails;

        $this->template->navigation = $this->navigationManager->getNavigation();

        $this->siteManager->siteDetails->maintenance ? $this->redirectPermanent('Homepage:default') : null;
    }

    public function renderDefault(string $path = null, int $id = null){

        $this->template->setFile(constant('ABSPATH') . $this->siteManager->siteDetails->theme . '/templates/Category/default.latte');
        $this->setLayout(constant('ABSPATH') . $this->siteManager->siteDetails->theme . '/templates/@layout.latte');

        $categoryData = $this->database->table($this->db_prefix.'categories')
            ->where('path', $path)
            ->limit(1)
            ->fetch();

        if ($categoryData->status === 0){
            $this->error();
        }

        $this->template->title = $this->siteManager->getTitle($categoryData->name);
        $this->template->name = $categoryData->name;
        $this->template->description = $categoryData->description;

        $this->template->articles = $this->categoryManager->getArticles($categoryData->ID);
    }
}
