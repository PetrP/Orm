<?php
/**
* @property string $name
* @property string $name2 
* @property array $name2 
* @property $name3 bool
* @property-read $name4s
* @property-write $name5 bool
* @property $test int
*/
class Article extends Entity
{
	public function getId()
	{
		return $this->id;
	}
	
	private function getTest()
	{
		return $this->id;
	}
}

class ArticleRepository extends Repository
{
	
}


$articleRepository = Factory::getRepository('Article');

$articleRepository->findByType($type);
$articleRepository->getById($id);




//$article = $articleRepository->getById(5);

//$articleRepository->persist($article);

$article = new Article;


$article->name = "10.01";

dd($article->name2);

exit;
/**/

