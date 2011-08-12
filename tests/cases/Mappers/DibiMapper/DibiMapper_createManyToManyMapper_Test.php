<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiMapper::createManyToManyMapper
 */
class DibiMapper_createManyToManyMapper_Test extends TestCase
{
	private $m;
	private $r;
	protected function setUp()
	{
		$this->r = new TestEntityRepository(new RepositoryContainer);
		$this->m = new DibiMapper_getConnection_DibiMapper(new TestsRepository(new RepositoryContainer));
	}

	public function test()
	{
		$mmm = $this->m->createManyToManyMapper('paramFirst', $this->r, 'paramSecond');
		$this->assertInstanceOf('Orm\DibiManyToManyMapper', $mmm);
		$this->assertAttributeInstanceOf('DibiConnection', 'connection', $mmm);
		$this->assertAttributeSame($this->m->getConnection(), 'connection', $mmm);
		$this->assertAttributeSame('tests_x_testentity', 'table', $mmm);
		$this->assertAttributeSame('param_first_id', 'childParam', $mmm);
		$this->assertAttributeSame('param_second_id', 'parentParam', $mmm);
	}

}
