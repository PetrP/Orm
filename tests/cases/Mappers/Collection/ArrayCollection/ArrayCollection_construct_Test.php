<?php

use Orm\ArrayCollection;

/**
 * @covers Orm\ArrayCollection::__construct
 */
class ArrayCollection_construct_Test extends ArrayCollection_Base_Test
{

	public function testEmpty()
	{
		$this->assertAttributeSame(array(), 'source', new ArrayCollection(array()));
	}

	public function test()
	{
		$e1 = new TestEntity;
		$e2 = new TestEntity;
		$e3 = new TestEntity;
		$this->assertAttributeSame(array($e1, $e2, $e3), 'source', new ArrayCollection(array($e1, $e2, $e3)));
		$this->assertAttributeSame(array($e1, $e2, $e3), 'source', new ArrayCollection(array('xxx' => $e1, 5 => $e2, 0 => $e3)));
		$this->assertAttributeSame(array($e1, $e3), 'source', new ArrayCollection(array($e1, $e1, $e3)));
	}

	public function testAscDesc()
	{
		$this->assertSame(Dibi::DESC, ArrayCollection::DESC);
		$this->assertSame(Dibi::ASC, ArrayCollection::ASC);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayCollection', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
