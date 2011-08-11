<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ArrayMapper::getData
 */
class ArrayMapper_getData_Test extends TestCase
{
	private $r;
	private $m;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
		$this->m = new ArrayMapper_getData_ArrayMapper($this->r);
	}

	public function test()
	{
		$data = $this->readAttribute($this->m, 'data');
		$this->assertSame(NULL, $data);
		$data = $this->m->_getData();
		$this->assertSame($data, $this->readAttribute($this->m, 'data'));
		$this->assertSame(2, count($data));
		$this->assertSame($this->r->getById(1), $data[1]);
		$this->assertSame($this->r->getById(2), $data[2]);
	}

	public function testCache()
	{
		$this->assertSame($this->m->_getData(), $this->m->_getData());
	}

	public function testRemove()
	{
		$this->m->remove($this->m->getById(1));
		$data = $this->m->_getData();
		$this->assertSame(1, count($data));
		$this->assertSame($this->m->getById(2), $data[2]);

		$data = $this->readAttribute($this->m, 'data');
		$this->assertSame(2, count($data));
		$this->assertSame($this->m->getById(2), $data[2]);
		$this->assertSame(NULL, $data[1]);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayMapper', 'getData');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
