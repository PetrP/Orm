<?php

use Orm\MetaData;
use Orm\RepositoryContainer;

/**
 * @covers Orm\MetaData::check
 */
class MetaData_check_Test extends TestCase
{
	public function test1()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('id', '');
		$this->assertNull($m->check(new RepositoryContainer));
	}

	public function test2()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('id', '')
			->setManyToMany('xxx')
		;
		$this->setExpectedException('Nette\InvalidStateException', 'xxx isn\'t repository in MetaData_Test_Entity::$id');
		$m->check(new RepositoryContainer);
	}

}
