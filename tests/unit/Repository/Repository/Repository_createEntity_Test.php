<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::createEntity
 */
class Repository_createEntity_Test extends TestCase
{

	public function test()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$this->setExpectedException('Nette\DeprecatedException', 'Orm\Repository::createEntity() is deprecated; use Orm\Repository::hydrateEntity() instead');
		$r->createEntity(array());
	}

}
