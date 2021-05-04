<?php

namespace App\Model;

use Nette;

class SiteManager{

	use Nette\SmartObject;

	private $db_prefix;
    private $database;
	private $httpRequest;

    private $title_structure = ' - ';
	public $siteDetails = null;

    public function __construct($db_prefix, Nette\Database\Explorer $database, Nette\Http\Request $httpRequest)
    {

        $this->db_prefix = $db_prefix;
        $this->database = $database;
		$this->httpRequest = $httpRequest;

		$this->setDetails();
    }

	public function setDetails()
	{
		$this->siteDetails = (object) array(
            'sitename' => $this->getSetting('sitename'),
            'description' => $this->getSetting('description'),
			'language' => $this->getSetting('site_language'),
            'author' => $this->getSetting('author'),
			'robots' => $this->getSetting('robots'),
            'showAuthor' => filter_var($this->getSetting('show_author'), FILTER_VALIDATE_BOOLEAN),
            'sitenameLocation' => $this->getSetting('sitename_title_location'),
			'maintenance' => filter_var($this->getSetting('maintenance'), FILTER_VALIDATE_BOOLEAN),
			'maintenanceMessage' => $this->getSetting('maintenance_message'),
			'theme' => 'themes/'.$this->getTheme()
        );
	}

	public function getSetting(string $name)
	{
		return $this->database->table($this->db_prefix.'settings')
			->select('value')
			->where('name', $name)
			->limit(1)
			->fetch()
			->value;
	}

	public function getTheme()
	{
		return $this->database->table($this->db_prefix.'themes')
			->select('name')
			->where('status', 1)
			->limit(1)
			->fetch()
			->name;
	}

	public function getTitle(string $title = null)
	{
		$sitename = $this->siteDetails->sitename;
		$seo = $this->siteDetails->sitenameLocation;

		if ($title){

			if ($seo === 'false'){

				return $title;

			}elseif ($seo === 'after'){

				return $title.$this->title_structure.$sitename;

			}elseif ($seo === 'before'){

				return $sitename.$this->title_structure.$title;
			}
		}else {

			return $sitename;
		}
	}

	public function setArticleHit(int $id)
	{
		$hits = $this->database->table($this->db_prefix.'articles')
			->select('hits')
			->where('ID', $id)
			->limit(1)
			->fetch()
			->hits;

		$hits++;

		$this->database->table($this->db_prefix.'articles')
			->where('ID', $id)
			->update([
				'hits' => $hits
			]);
	}

	public function setContentPaths(string $content)
	{
		$basePath = $this->httpRequest->getUrl()->getBasePath();

		$protocols  = '[a-zA-Z0-9\-]+:';
		$attributes = array('href=', 'src=', 'poster=');

		foreach ($attributes as $attribute)
		{
			if (strpos($content, $attribute) !== false)
			{
				$regex  = '#\s' . $attribute . '"(?!/|' . $protocols . '|\#|\')([^"]*)"#m';
				$content = preg_replace($regex, ' ' . $attribute . '"' . $basePath.'$1"', $content);
			}
		}

		return $content;
	}
}

?>