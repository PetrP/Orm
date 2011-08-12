<?php

use Orm\ArrayCollection;

/**
 * @covers Orm\ArrayCollection::fetchPairs
 */
class ArrayCollection_fetchPairs_Test extends ArrayCollection_Base_Test
{

	public function test1()
	{
		$this->assertSame(array(
			2 => 'a',
			1 => 'b',
			3 => 'a',
			4 => 'b',
		), $this->c->fetchPairs('int', 'string'));
	}

	public function test2()
	{
		$this->assertSame(array(
			0 => 'a',
			1 => 'b',
			2 => 'a',
			3 => 'b',
		), $this->c->fetchPairs(NULL, 'string'));
	}

	public function test3()
	{
		$this->assertSame(array(
			'a' => 'a',
			'b' => 'b',
		), $this->c->fetchPairs('string', 'string'));
	}

	public function testBad1()
	{
		$this->setExpectedException('Nette\InvalidArgumentException', 'Value or both columns must be specified.');
		$this->c->fetchPairs(NULL, NULL);
	}

	public function testBad2()
	{
		$this->setExpectedException('Nette\InvalidArgumentException', 'Value or both columns must be specified.');
		$this->c->fetchPairs('string');
	}

	public function testUnexistKey()
	{
		$this->setExpectedException('Nette\InvalidArgumentException', "Unknown key column 'unexist'.");
		$this->c->fetchPairs('unexist', 'string');
	}

	public function testUnexistValue()
	{
		$this->setExpectedException('Nette\InvalidArgumentException', "Unknown value column 'unexist'.");
		$this->c->fetchPairs('string', 'unexist');
	}

	public function testEmpty()
	{
		$this->c->applyLimit(0);
		$this->assertSame(array(), $this->c->fetchPairs('int', 'string'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayCollection', 'fetchPairs');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
