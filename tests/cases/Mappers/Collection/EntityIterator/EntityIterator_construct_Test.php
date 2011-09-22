<?php

use Orm\EntityIterator;

/**
 * @covers Orm\EntityIterator::__construct
 */
class EntityIterator_construct_Test extends EntityIterator_Base_Test
{
	public function test1()
	{
		$this->assertAttributeSame($this->r, 'repository', $this->i);
	}

	public function test2()
	{
		$this->assertInstanceOf('Traversable', $this->i);
		$this->assertInstanceOf('Countable', $this->i);
	}

	public function testAnyTraversable1()
	{
		$i = new EntityIterator($this->r, new ArrayIterator(array(array('id' => 5), array('id' => 6))));
		$all = iterator_to_array($i);
		$this->assertSame(2, count($all));
		$this->assertSame(5, $all[0]->id);
		$this->assertSame(6, $all[1]->id);
	}

	public function testAnyTraversable2()
	{
		$i = new EntityIterator($this->r, new IteratorIterator(new ArrayIterator(array(array('id' => 5), array('id' => 6)))));
		$all = iterator_to_array($i);
		$this->assertSame(2, count($all));
		$this->assertSame(5, $all[0]->id);
		$this->assertSame(6, $all[1]->id);
	}

	public function testAnyTraversable3()
	{
		$i = new EntityIterator($this->r, new EntityIterator_Base_IteratorAggregate(array(array('id' => 5), array('id' => 6))));
		$all = iterator_to_array($i);
		$this->assertSame(2, count($all));
		$this->assertSame(5, $all[0]->id);
		$this->assertSame(6, $all[1]->id);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EntityIterator', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
