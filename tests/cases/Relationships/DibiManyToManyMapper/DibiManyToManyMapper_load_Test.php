<?php

use Orm\DibiManyToManyMapper;
use Orm\ManyToMany;

/**
 * @covers Orm\DibiManyToManyMapper::load
 */
class DibiManyToManyMapper_load_Test extends DibiManyToManyMapper_Connected_Test
{

	public function test1()
	{
		$this->d->addExpected('query', true, 'SELECT `y` FROM `t` WHERE `x` = \'1\'');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('y' => 9), true);
		$this->d->addExpected('fetch', array('y' => 8), true);
		$this->d->addExpected('fetch', NULL, true);
		$r = $this->mm->load($this->e, NULL);
		$this->assertSame(array(9, 8), $r);
	}

	public function test2()
	{
		$this->d->addExpected('query', true, 'SELECT `y` FROM `t` WHERE `x` = \'1\'');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('y' => 4), true);
		$this->d->addExpected('fetch', array('y' => 5), true);
		$this->d->addExpected('fetch', NULL, true);
		$r = $this->mm->load($this->e, array(3), NULL);
		$this->assertSame(array(4, 5), $r);
	}

	public function testEmpty()
	{
		$this->d->addExpected('query', true, 'SELECT `y` FROM `t` WHERE `x` = \'1\'');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', NULL, true);
		$this->assertSame(array(), $this->mm->load($this->e, NULL));
	}

	public function testBoth()
	{
		$this->mm->attach(new MockRelationshipMetaDataManyToManyBoth('TestEntity', 'foo', 'foo', 'foo'));
		$this->d->addExpected('query', true, 'SELECT `y` FROM `t` WHERE `x` = \'1\'');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('y' => 9), true);
		$this->d->addExpected('fetch', array('y' => 8), true);
		$this->d->addExpected('fetch', NULL, true);
		$this->d->addExpected('query', true, 'SELECT `x` FROM `t` WHERE `y` = \'1\'');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('x' => 10), true);
		$this->d->addExpected('fetch', array('x' => 11), true);
		$this->d->addExpected('fetch', array('x' => 9), true);
		$this->d->addExpected('fetch', NULL, true);
		$r = $this->mm->load($this->e, NULL);
		$this->assertSame(array(9, 8, 10, 11), $r);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiManyToManyMapper', 'load');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
