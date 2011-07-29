<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::getRepositoryName
 */
class Repository_getRepositoryName_Test extends TestCase
{

	public function test()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$this->setExpectedException('Nette\DeprecatedException', 'Orm\Repository::getRepositoryName() is deprecated; use get_class($repository) instead');
		$r->getRepositoryName();
	}

}
