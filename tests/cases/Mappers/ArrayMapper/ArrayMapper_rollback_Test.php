<?php

use Orm\RepositoryContainer;
use Orm\ValidationHelper;

/**
 * @covers Orm\ArrayMapper::rollback
 */
class ArrayMapper_rollback_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new TestsMapper(new TestsRepository(new RepositoryContainer));
	}

	public function test()
	{
		$this->m->persist($e = new TestEntity);
		$data = $this->readAttribute($this->m, 'data');
		$this->assertSame(3, count($data));
		$this->assertInstanceOf('TestEntity', $data[1]);
		$this->assertSame($e, $data[3]);

		$this->m->rollback();
		$this->assertSame(NULL, $this->readAttribute($this->m, 'data'));

		$this->m->findAll();
		$data = $this->readAttribute($this->m, 'data');
		$this->assertSame(3, count($data));
		$this->assertInstanceOf('TestEntity', $data[1]);
		$this->assertSame(NULL, $data[3]);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayMapper', 'rollback');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
