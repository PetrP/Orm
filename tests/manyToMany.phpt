<?php

require dirname(__FILE__) . '/base.php';

/**
 * @property string $name
 * @property ArticlesToTags $tags
 */
class Article extends Entity
{
	public function __construct($name = NULL)
	{
		parent::__construct();
		$this->name = $name;
		$this->tags = new ArticlesToTags($this);
	}
}

/**
 * @property string $name
 */
class Tag extends Entity
{
	public function __construct($name = NULL)
	{
		parent::__construct();
		$this->name = $name;
	}
}

class ArticlesRepository extends Repository {}
class TagsRepository extends Repository {}

abstract class BaseMapper extends ArrayMapper
{
	protected function loadData()
	{
		return array();
	}
	protected function saveData(array $data)
	{
		dt($data);
	}
}

class ArticlesMapper extends BaseMapper {}
class TagsMapper extends BaseMapper {}

class ArticlesToTags extends ManyToMany
{
	public function persist()
	{
		dt('persist');
	}
	protected function load()
	{
		dt('load');
		return array();
	}
}




$a = new Article('Clanek1');
$a->tags->add(new Tag('kategorie'));


Model::get()->articles->persist($a);
Model::get()->articles->flush();


__halt_compiler();
------EXPECT------
string(4) "load"

array(1) {
	1 => NULL
}

string(7) "persist"

array(1) {
	1 => array(2) {
		"id" => int(1)
		"name" => string(7) "Clanek1"
	}
}


