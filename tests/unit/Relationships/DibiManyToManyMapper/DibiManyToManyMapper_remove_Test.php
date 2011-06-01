<?php

use Orm\DibiManyToManyMapper;

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers Orm\DibiManyToManyMapper::remove
 * @covers Orm\DibiManyToManyMapper::getParentParam
 * @covers Orm\DibiManyToManyMapper::getChildParam
 */
class DibiManyToManyMapper_remove_Test extends DibiManyToManyMapper_Connected_Test
{

	public function testParentIsFirst()
	{
		$this->mm->setParams(true);
		$this->d->addExpected('query', true, 'DELETE FROM `t` WHERE `x` = \'1\' AND `y` IN (1, 2, 3)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->mm->remove($this->e, array(1, 2, 3));
	}

	public function testParentNotFirst()
	{
		$this->mm->setParams(false);
		$this->d->addExpected('query', true, 'DELETE FROM `t` WHERE `y` = \'1\' AND `x` IN (3)');
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


}
