<?php

use Orm\ArrayCollection;

/**
 * @covers Orm\ArrayCollection::getResult
 * @covers Orm\ArrayCollection::_sort
 */
class ArrayCollection_getResult_orderBy_Test extends ArrayCollection_Base_Test
{

	public function testAsc()
	{
		$this->c->orderBy('int', Dibi::ASC);
		$this->assertSame(array(
			$this->e[1],
			$this->e[0],
			$this->e[2],
			$this->e[3],
		), $this->c->getResult());
	}

	public function testDesc()
	{
		$this->c->orderBy('int', Dibi::DESC);
		$this->assertSame(array(
			$this->e[3],
			$this->e[2],
			$this->e[0],
			$this->e[1],
		), $this->c->getResult());
	}

	public function testMultiAscAsc()
	{
		$this->c->orderBy('string', Dibi::ASC);
		$this->c->orderBy('int', Dibi::ASC);
		$this->assertSame(array(
			$this->e[0],
			$this->e[2],
			$this->e[1],
			$this->e[3],
		), $this->c->getResult());
	}

	public function testMultiDescAsc()
	{
		$this->c->orderBy('string', Dibi::DESC);
		$this->c->orderBy('int', Dibi::ASC);
		$this->assertSame(array(
			$this->e[1],
			$this->e[3],
			$this->e[0],
			$this->e[2],
		), $this->c->getResult());
	}

	public function testMultiAscDesc()
	{
		$this->c->orderBy('string', Dibi::ASC);
		$this->c->orderBy('int', Dibi::DESC);
		$this->assertSame(array(
			$this->e[2],
			$this->e[0],
			$this->e[3],
			$this->e[1],
		), $this->c->getResult());
	}


	public function testMultiDescDesc()
	{
		$this->c->orderBy('string', Dibi::DESC);
		$this->c->orderBy('int', Dibi::DESC);
		$this->assertSame(array(
			$this->e[3],
			$this->e[1],
			$this->e[2],
			$this->e[0],
		), $this->c->getResult());
	}

	public function testDateDesc()
	{
		$this->e[0]->date = '-4 days';
		$this->e[1]->date = '-1 days';
		$this->e[2]->date = 'now';
		$this->e[3]->date = '+5 days';
		$this->c->orderBy('date', Dibi::DESC);
		$this->assertSame(array(
			$this->e[3],
			$this->e[2],
			$this->e[1],
			$this->e[0],
		), $this->c->getResult());
	}

	public function testDateAsc()
	{
		$this->e[3]->date = '-4 days';
		$this->e[1]->date = '-1 days';
		$this->e[2]->date = 'now';
		$this->e[0]->date = '+5 days';
		$this->c->orderBy('date', Dibi::ASC);
		$this->assertSame(array(
			$this->e[3],
			$this->e[1],
			$this->e[2],
			$this->e[0],
		), $this->c->getResult());
	}

	public function testNullAsc()
	{
		$this->e[0]->int = NULL;
		$this->e[3]->int = NULL;
		$this->c->orderBy('int', Dibi::ASC);
		$this->assertSame(array(
			$this->e[3],
			$this->e[0],
			$this->e[1],
			$this->e[2],
		), $this->c->getResult());
	}

	public function testNullDesc()
	{
		$this->e[0]->int = NULL;
		$this->e[3]->int = NULL;
		$this->c->orderBy('int', Dibi::DESC);
		$this->assertSame(array(
			$this->e[2],
			$this->e[1],
			$this->e[3],
			$this->e[0],
		), $this->c->getResult());
	}

	public function testBadKey()
	{
		$this->c->orderBy('unexist');
		$this->setExpectedException('Orm\InvalidArgumentException', "'unexist' is not key");
		$this->c->getResult();
	}

	public function testBadValue()
	{
		$this->e[0]->mixed = new ArrayIterator(array());
		$this->e[1]->mixed = new ArrayIterator(array());
		$this->c->orderBy('mixed');
		$this->setExpectedException('Orm\InvalidArgumentException', "ArrayCollection_Entity::\$mixed contains non-sortable value, ArrayIterator");
		$this->c->getResult();
	}

	public function testGetter()
	{
		$this->c->orderBy('getter');
		$this->assertSame(array(
			$this->e[1],
			$this->e[0],
			$this->e[2],
			$this->e[3],
		), $this->c->getResult());
	}

	public function testSubEntity()
	{
		$this->e[0]->mixed = $this->e[2];
		$this->e[1]->mixed = $this->e[3];
		$this->c->orderBy('mixed->string');
		$this->assertSame(array(
			$this->e[2], // NULL
			$this->e[3], // NULL
			$this->e[0], // a
			$this->e[1], // b
		), $this->c->getResult());
	}

	public function testSubEntityBadKeyA()
	{
		$this->e[0]->mixed = $this->e[2];
		$this->e[1]->mixed = $this->e[3];
		$this->c->orderBy('mixed->bad');
		$this->setExpectedException('Orm\InvalidArgumentException', "'bad' is not key in 'mixed->bad'");
		$this->c->getResult();
	}

	public function testSubEntityBadKeyB()
	{
		$this->e[0]->mixed = $this->e[2];
		$c = new ArrayCollection(array(new TestEntity, $this->e[0]));
		$c->orderBy('mixed->string');
		$this->setExpectedException('Orm\InvalidArgumentException', "'mixed' is not key in 'mixed->string'");
		$c->getResult();
	}

	public function testPhpBug50688()
	{
		$this->e[2]->phpBug50688 = '1';
		$this->e[1]->phpBug50688 = '2';
		$this->e[3]->phpBug50688 = '3';
		$this->e[0]->phpBug50688 = '4';
		$this->c->orderBy('phpBug50688');
		$this->assertSame(array(
			$this->e[2],
			$this->e[1],
			$this->e[3],
			$this->e[0],
		), $this->c->fetchAll());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayCollection', 'getResult');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
