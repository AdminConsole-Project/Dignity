<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Routing\Route;

final class RouterFactory{

	use Nette\StaticClass;

	private $db_prefix;
    private $database;

    public function __construct($db_prefix, Nette\Database\Explorer $database)
    {

        $this->db_prefix = $db_prefix;
        $this->database = $database;

    }

	public function createRouter(): RouteList
	{
		$router = new RouteList;
		$router->addRoute('', 'Homepage:default');
		$router->addRoute('article/<path>/', [
			'presenter' => 'Article',
			'action' => 'default',
			null => [
				Route::FILTER_IN => function(array $paramas){

					$sql = $this->database->table($this->db_prefix.'articles')
						->select('alias')
						->where('alias', $paramas['path'])
						->fetch();

					if (!$sql){
						return null;
					}

					$paramas['path'] = $sql->alias;
					return $paramas;
				},
				Route::FILTER_OUT => function(array $paramas){

					if (isset($paramas['id'])){

						$sql = $this->database->table($this->db_prefix.'articles')
							->select('alias')
							->where('status', 1)
							->where('ID', $paramas['id'])
							->fetch();

					}elseif (isset($paramas['path'])){

						$sql = $this->database->table($this->db_prefix.'articles')
							->select('alias')
							->where('status', 1)
							->where('alias', $paramas['path'])
							->fetch();
					}

					if (!$sql){
						return null;
					}

					unset($paramas['id']);
					$paramas['path'] = $sql->alias;
					return $paramas;
				}
			]
		]);
		$router->addRoute('category/<path .+>/', [
			'presenter' => 'Category',
			'action' => 'default',
			null => [
				Route::FILTER_IN => function(array $paramas){

					$sql = $this->database->table($this->db_prefix.'categories')
						->select('path')
						->where('path', $paramas['path'])
						->fetch();

					if (!$sql){
						return null;
					}

					$paramas['path'] = $sql->path;
					return $paramas;
				},
				Route::FILTER_OUT => function(array $paramas){

					if (isset($paramas['id'])){

						$sql = $this->database->table($this->db_prefix.'categories')
							->select('path')
							->where('status', 1)
							->where('ID', $paramas['id'])
							->fetch();

					}elseif (isset($paramas['path'])){

						$sql = $this->database->table($this->db_prefix.'categories')
							->select('path')
							->where('status', 1)
							->where('path', $paramas['path'])
							->fetch();
					}

					if (!$sql){
						return null;
					}


					unset($paramas['id']);
					$paramas['path'] = $sql->path;
					return $paramas;
				}
			]
		]);
		$router->addRoute('<path>/', [
			'presenter' => 'Page',
			'action' => 'default',
			null => [
				Route::FILTER_IN => function(array $paramas){

					$sql = $this->database->table($this->db_prefix.'pages')
						->select('alias')
						->where('alias', $paramas['path'])
						->fetch();

					if (!$sql){
						return null;
					}

					$paramas['path'] = $sql->alias;
					return $paramas;
				},
				Route::FILTER_OUT => function(array $paramas){

					if (isset($paramas['id'])){

						$sql = $this->database->table($this->db_prefix.'pages')
							->select('alias')
							->where('status', 1)
							->where('ID', $paramas['id'])
							->fetch();

					}elseif (isset($paramas['path'])){

						$sql = $this->database->table($this->db_prefix.'pages')
							->select('alias')
							->where('status', 1)
							->where('alias', $paramas['path'])
							->fetch();
					}

					if (!$sql){
						return null;
					}

					unset($paramas['id']);
					$paramas['path'] = $sql->alias;
					return $paramas;
				}
			]
		]);
		return $router;
	}
}
