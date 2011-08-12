<?php

use Orm\DibiManyToManyMapper;

/**
 * @covers Orm\DibiManyToManyMapper::remove
 */
class DibiManyToManyMapper_remove_Test extends DibiManyToManyMapper_Connected_Test
{

	public function test1()
	{
		$this->d->addExpected('query', true, 'DELETE FROM `t` WHERE `x` = \'1\' AND `y` IN (1, 2, 3)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->mm->remove($this->e, array(1, 2, 3));
	}

	public function test2()
	{
		$this->d->addExpected('query', true, 'DELETE FROM `t` WHERE `x` = \'1\' AND `y` IN (3)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->mm->remove($this->e, array(3));
	}

	public function testEmpty()
	{
		$this->d->addExpected('query', true, 'DELETE FROM `t` WHERE `x` = \'1\' AND `y` IN (NULL)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->mm->remove($this->e, array());
		$this->assertTrue(true);
	}

	public function testReturns()
	{
		$this->d->addExpected('query', true, 'DELETE FROM `t` WHERE `x` = \'1\' AND `y` IN (NULL)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->assertSame(NULL, $this->mm->remove($this->e, array()));
	}


	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiManyToManyMapper', 'remove');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
