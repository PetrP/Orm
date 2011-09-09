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

	public function testIdArray()
	{
		$this->m->array = array(
			array('id' => 1),
			array('id' => 2),
		);
		$data = $this->m->_getData();
		$this->assertSame(2, count($data));
		$this->assertSame($this->m->getById(1), $data[1]);
		$this->assertSame($this->m->getById(2), $data[2]);
	}

	public function testIdArray2()
	{
		$this->m->array = array(
			'foo' => array('id' => 1),
			'bar' => array('id' => 2),
		);
		$data = $this->m->_getData();
		$this->assertSame(2, count($data));
		$this->assertSame($this->m->getById(1), $data[1]);
		$this->assertSame($this->m->getById(2), $data[2]);
	}

	public function testIdKey()
	{
		$this->m->array = array(
			1 => array(),
			2 => array(),
		);
		$data = $this->m->_getData();
		$this->assertSame(2, count($data));
		$this->assertSame($this->m->getById(1), $data[1]);
		$this->assertSame($this->m->getById(2), $data[2]);
	}

	public function testIdCombine()
	{
		$this->m->array = array(
			'foo' => array('id' => 1),
			2 => array(),
		);
		$data = $this->m->_getData();
		$this->assertSame(2, count($data));
		$this->assertSame($this->m->getById(1), $data[1]);
		$this->assertSame($this->m->getById(2), $data[2]);
	}

	public function testIdBoth()
	{
		$this->m->array = array(
			1 => array('id' => 1),
			2 => array('id' => 2),
		);
		$data = $this->m->_getData();
		$this->assertSame(2, count($data));
		$this->assertSame($this->m->getById(1), $data[1]);
		$this->assertSame($this->m->getById(2), $data[2]);
	}

	public function testNull()
	{
		$this->m->array = array(
			1 => NULL,
			2 => array(),
			array('id' => 3),
			4 => NULL,
		);
		$data = $this->m->_getData();
		$this->assertSame(2, count($data));
		$this->assertSame(NULL, $this->m->getById(1));
		$this->assertSame($this->m->getById(2), $data[2]);
		$this->assertSame($this->m->getById(3), $data[3]);
		$this->assertSame(NULL, $this->m->getById(4));
		$data = $this->readAttribute($this->m, 'data');
		$this->assertSame(4, count($data));
		$this->assertSame(NULL, $data[1]);
		$this->assertSame(NULL, $data[4]);
	}

	public function testIdTwice1()
	{
		$this->m->array = array(
			1 => NULL,
			array('id' => 1),
		);
		$this->setExpectedException('Orm\BadReturnException', "ArrayMapper_getData_ArrayMapper::loadData() must return each id only once; id '1' is contained twice.");
		$this->m->_getData();
	}

	public function testIdTwice2()
	{
		$this->m->array = array(
			array('id' => 2),
			array('id' => 2),
		);
		$this->setExpectedException('Orm\BadReturnException', "ArrayMapper_getData_ArrayMapper::loadData() must return each id only once; id '2' is contained twice.");
		$this->m->_getData();
	}

	public function testIdTwice3()
	{
		$this->m->array = array(
			array('id' => 3),
			3 => array(),
		);
		$this->setExpectedException('Orm\BadReturnException', "ArrayMapper_getData_ArrayMapper::loadData() must return each id only once; id '3' is contained twice.");
		$this->m->_getData();
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
