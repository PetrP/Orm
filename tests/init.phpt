<?php

require dirname(__FILE__) . '/base.php';

/**
* @property-read int $id
* @property string $text
* @property int $int
* @property string $char
*/
class Test extends Entity
{
	public function setChar($char)
	{
		if (strlen($char) !== 1)
		{
			throw new LengthException();
		}
		return $this->setValue('char', $char);
	}
}


class TestRepository extends Repository
{
	
}

class TestMapper extends SimpleMapper
{
	
}









/*
Model::init(array(
	'User' => 'Users',
	'Email' => 'Emails',
));*/

/*
for ($i=1000;$i--;)
	Model::getRepository('test')->getById(153548);
*/
foreach (Model::getRepository('test')->findAll()->limit(30) as $x)
{
	if (--$i < 1) break;
}