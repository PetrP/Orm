<?php

use Orm\DibiManyToManyMapper;

/**
 * @covers Orm\DibiManyToManyMapper::add
 */
class DibiManyToManyMapper_add_Test extends DibiManyToManyMapper_Connected_Test
{

	public function test1()
	{
		$this->d->addExpected('query', true, 'INSERT INTO `t` (`x`, `y`) VALUES (1, 1)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('query', true, 'INSERT INTO `t` (`x`, `y`) VALUES (1, 2)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('query', true, 'INSERT INTO `t` (`x`, `y`) VALUES (1, 3)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->mm->add($this->e, array(1, 2, 3));
	}

	public function test2()
	{
		$this->d->addExpected('query', true, 'INSERT INTO `t` (`x`, `y`) VALUES (1, 3)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->mm->add($this->e, array(3));
	}

	public function testEmpty()
	{
		$this->mm->add($this->e, array());
		$this->assertTrue(true);
	}

	public function testReturns()
	{
		$this->assertSame(NULL, $this->mm->add($this->e, array()));
	}


	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiManyToManyMapper', 'add');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
