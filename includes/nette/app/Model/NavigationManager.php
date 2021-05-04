<?php

namespace App\Model;

use Nette;
use Nette\Utils\Json;

class NavigationManager{

	use Nette\SmartObject;

	private $db_prefix;
    private $database;

    public function __construct($db_prefix, Nette\Database\Explorer $database)
    {

        $this->db_prefix = $db_prefix;
        $this->database = $database;

    }

	public function getNavigationStructure()
	{
		return $this->database->table($this->db_prefix.'navigation')
			->select('structure')
			->where('status', 1)
			->order('date ASC')
			->limit(1)
			->fetch()
			->structure ?? [];
	}

	public function getNavigation()
	{
		$structure = $this->getNavigationStructure();

		if ($structure){

			$structure = Json::decode($structure);

		}

		return $structure;
	}
}

?>