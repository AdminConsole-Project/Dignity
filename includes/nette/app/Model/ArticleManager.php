<?php

namespace App\Model;

use Nette;

class ArticleManager{

	use Nette\SmartObject;

	private $db_prefix;
    private $database;

    public function __construct($db_prefix, Nette\Database\Explorer $database)
    {

        $this->db_prefix = $db_prefix;
        $this->database = $database;

    }

	public function getAuthor(int $id, string $select)
	{
		return $this->database->table($this->db_prefix.'users')
			->select($select)
			->where('ID', $id)
			->fetch()
			->$select;
	}
}

?>