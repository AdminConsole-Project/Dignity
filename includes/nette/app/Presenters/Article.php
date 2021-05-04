<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Latte;
use App\Model\SiteManager;
use App\Model\NavigationManager;
use App\Model\ArticleManager;

final class ArticlePresenter extends Nette\Application\UI\Presenter{

    private $db_prefix;
    private $database;
    private $siteManager;
    private $navigationManager;
    private $articleManager;

    private $siteDetails;

    public function __construct($db_prefix, Nette\Database\Explorer $database, SiteManager $siteManager, NavigationManager $navigationManager, ArticleManager $articleManager)
    {

        $this->db_prefix = $db_prefix;
        $this->database = $database;
        $this->siteManager = $siteManager;
        $this->navigationManager = $navigationManager;
        $this->articleManager = $articleManager;

    }

    public function beforeRender()
    {
        $this->template->siteDetails = $this->siteManager->siteDetails;

        $this->template->navigation = $this->navigationManager->getNavigation();

        $this->siteManager->siteDetails->maintenance ? $this->redirectPermanent('Homepage:default') : null;
    }

    public function renderDefault(string $path = null, int $id = null){

        $this->template->setFile(constant('ABSPATH') . $this->siteManager->siteDetails->theme . '/templates/Article/default.latte');
        $this->setLayout(constant('ABSPATH') . $this->siteManager->siteDetails->theme . '/templates/@layout.latte');

        $articleData = $this->database->table($this->db_prefix.'articles')
            ->where('alias', $path)
            ->limit(1)
            ->fetch();

        /*if ($articleData->category !== 0){
            $this->redirectPermanent('Category:default', ['id' => $articleData->category]);
        }*/

        if ($articleData->status === 0){
            $this->error();
        }

        $this->siteManager->setArticleHit($articleData->ID);

        $content = $this->siteManager->setContentPaths($articleData->content);

        $this->template->title = $this->siteManager->getTitle($articleData->title);
        $this->template->content = new Latte\Runtime\Html($content);

        $this->template->content_details = (object) array(
            'title' => $articleData->title,
            'category' => '',
            'author' => (object) array(
                'username' => $this->articleManager->getAuthor($articleData->author, 'username'),
                'firstname' => $this->articleManager->getAuthor($articleData->author, 'firstname'),
                'lastname' => $this->articleManager->getAuthor($articleData->author, 'lastname')
            ),
            'hits' =>  $articleData->hits,
            'date' =>  $articleData->date
        );
    }
}
