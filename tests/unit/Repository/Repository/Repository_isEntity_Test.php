<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::isEntity
 */
class Repository_isEntity_Test extends TestCase
{

	public function test()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$this->setExpectedException('Nette\DeprecatedException', 'Orm\Repository::isEntity() is deprecated; use Orm\Repository::isAttachableEntity() instead');
		$r->isEntity(new TestEntity);
	}

}
