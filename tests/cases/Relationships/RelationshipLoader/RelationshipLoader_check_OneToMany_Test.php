<?php

use Orm\RelationshipLoader;
use Orm\MetaData;
use Orm\RepositoryContainer;

/**
 * @covers Orm\RelationshipLoader::check
 */
class RelationshipLoader_check_OneToMany_Test extends TestCase
{

	public function testCheckRepo()
	{
		$rl = new RelationshipLoader(MetaData::OneToMany, 'Orm\OneToMany', 'repo', '', 'Entity', 'foo');
		$this->setExpectedException('Nette\InvalidStateException', 'repo isn\'t repository in Entity::$foo');
		$rl->check(new RepositoryContainer);
	}

}
