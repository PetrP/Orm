<?php

use Orm\DibiManyToManyMapper;

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers Orm\DibiManyToManyMapper::add
 * @covers Orm\DibiManyToManyMapper::getParentParam
 * @covers Orm\DibiManyToManyMapper::getChildParam
 */
class DibiManyToManyMapper_add_Test extends DibiManyToManyMapper_Connected_Test
{

	public function testParentIsFirst()
	{
		$this->mm->setParams(true);
		$this->d->addExpected('query', true, 'INSERT INTO `t` (`x`, `y`) VALUES (1, 1)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('query', true, 'INSERT INTO `t` (`x`, `y`) VALUES (1, 2)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('query', true, 'INSERT INTO `t` (`x`, `y`) VALUES (1, 3)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->mm->add($this->e, array(1, 2, 3));
	}

	public function testParentNotFirst()
	{
		$this->mm->setParams(false);
		$this->d->addExpected('query', true, 'INSERT INTO `t` (`y`, `x`) VALUES (1, 3)');
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


}
