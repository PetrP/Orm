<?php

use Orm\DibiManyToManyMapper;
use Orm\RelationshipMetaDataManyToMany;
use Orm\ManyToMany;

/**
 * @covers Orm\DibiManyToManyMapper::remove
 */
class DibiManyToManyMapper_remove_Test extends DibiManyToManyMapper_Connected_Test
{

	public function test1()
	{
		$this->d->addExpected('query', true, 'DELETE FROM `t` WHERE `x` = \'1\' AND `y` IN (1, 2, 3)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->assertNull($this->mm->remove($this->e, array(1, 2, 3), NULL));
	}

	public function test2()
	{
		$this->d->addExpected('query', true, 'DELETE FROM `t` WHERE `x` = \'1\' AND `y` IN (3)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->assertNull($this->mm->remove($this->e, array(3), NULL));
	}

	public function testEmpty()
	{
		$this->d->addExpected('query', true, 'DELETE FROM `t` WHERE `x` = \'1\' AND `y` IN (NULL)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->assertNull($this->mm->remove($this->e, array(), NULL));
	}

	public function testReturns()
	{
		$this->d->addExpected('query', true, 'DELETE FROM `t` WHERE `x` = \'1\' AND `y` IN (NULL)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->assertSame(NULL, $this->mm->remove($this->e, array(), NULL));
	}

	public function testBoth()
	{
		$this->mm->attach(new MockRelationshipMetaDataManyToManyBoth('TestEntity', 'foo', 'foo', 'foo'));
		$this->d->addExpected('query', true, 'DELETE FROM `t` WHERE ( `x` = \'1\' AND `y` IN (1, 2, 3) ) OR (`y` = \'1\' AND `x` IN (1, 2, 3))');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->assertNull($this->mm->remove($this->e, array(1, 2, 3), NULL));
	}

	public function testInverseSide()
	{
		$this->mm->attach(new RelationshipMetaDataManyToMany('foo', 'foo', 'foo', 'foo', NULL, RelationshipMetaDataManyToMany::MAPPED_THERE));
		$this->setExpectedException('Orm\NotSupportedException', 'Orm\IManyToManyMapper::remove() has not supported on inverse side.');
		$this->mm->remove($this->e, array(), NULL);
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
