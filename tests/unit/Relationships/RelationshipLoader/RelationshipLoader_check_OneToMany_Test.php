<?php

use Orm\RelationshipLoader;
use Orm\MetaData;
use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

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
