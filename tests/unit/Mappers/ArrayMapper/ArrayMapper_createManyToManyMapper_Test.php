<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\ArrayMapper::createManyToManyMapper
 */
class ArrayMapper_createManyToManyMapper_Test extends TestCase
{
	private $m;
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
		$this->m = new TestsMapper($this->r);
	}

	public function testReturn()
	{
		$this->assertInstanceOf('Orm\ArrayManyToManyMapper', $this->m->createManyToManyMapper('f', $this->r, 's'));
	}

}
