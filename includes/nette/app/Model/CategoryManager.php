<?php

namespace App\Model;

use Nette;

class CategoryManager{

	use Nette\SmartObject;

	private $db_prefix;
    private $database;

    public function __construct($db_prefix, Nette\Database\Explorer $database)
    {

        $this->db_prefix = $db_prefix;
        $this->database = $database;

    }

	public function getArticles(int $id)
	{
		return $this->database->table($this->db_prefix.'articles')
			->where('category', $id)
			->order('date DESC');
	}
}

?>