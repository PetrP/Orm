<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Repository::getEntityClassName
 */
class Repository_getEntityClassName_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$this->r = new Repository_getEntityClassNamesRepository(new RepositoryContainer);
	}

	public function testByProperty()
	{
		$this->r->entityClassName = 'Haha';
		$this->assertSame('Haha', $this->r->getEntityClassName());
		$this->assertSame('Haha', $this->r->getEntityClassName(array()));
	}

	public function testDefault()
	{
		$this->r->entityClassName = NULL;
		$this->assertSame('repository_getentityclassname', $this->r->getEntityClassName());
		$this->assertSame('repository_getentityclassname', $this->r->getEntityClassName(array()));
	}

}
