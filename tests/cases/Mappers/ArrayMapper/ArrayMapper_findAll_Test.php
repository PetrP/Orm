<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ArrayMapper::findAll
 */
class ArrayMapper_findAll_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new ArrayMapper_findAll_ArrayMapper(new TestsRepository(new RepositoryContainer));
	}

	public function testReturn()
	{
		$this->assertInstanceOf('Orm\ArrayCollection', $this->m->findAll());
	}

	public function testContain1()
	{
		$a = $this->m->findAll()->fetchPairs('id', 'id');
		$this->assertSame(array(1 => 1, 2 => 2), $a);
	}

	public function testContain2()
	{
		unset($this->m->array[2]);
		$a = $this->m->findAll()->fetchPairs('id', 'id');
		$this->assertSame(array(1 => 1), $a);
	}

	public function testContain3()
	{
		unset($this->m->array[2]);
		unset($this->m->array[1]);
		$a = $this->m->findAll()->fetchPairs('id', 'id');
		$this->assertSame(array(), $a);
	}

	public function testContain4()
	{
		unset($this->m->array[2]);
		unset($this->m->array[1]);
		$this->m->array[13] = array('id' => 13);
		$a = $this->m->findAll()->fetchPairs('id', 'id');
		$this->assertSame(array(13 => 13), $a);
	}

	public function testSource()
	{
		$c = $this->m->findAll();
		$source = $this->readAttribute($c, 'source');
		$this->assertSame(2, count($source));
		$this->assertInstanceOf('TestEntity', $source[0]);
		$this->assertInstanceOf('TestEntity', $source[1]);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayMapper', 'findAll');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
