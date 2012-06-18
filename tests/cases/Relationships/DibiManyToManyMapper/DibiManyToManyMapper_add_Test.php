<?php

use Orm\DibiManyToManyMapper;
use Orm\RelationshipMetaDataManyToMany;

/**
 * @covers Orm\DibiManyToManyMapper::add
 */
class DibiManyToManyMapper_add_Test extends DibiManyToManyMapper_Connected_Test
{

	public function test1()
	{
		$this->d->addExpected('query', true, 'INSERT IGNORE INTO `t` (`x`, `y`) VALUES (1, 1) , (1, 2) , (1, 3)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->assertNull($this->mm->add($this->e, array(1, 2, 3), NULL));
	}

	public function test2()
	{
		$this->d->addExpected('query', true, 'INSERT IGNORE INTO `t` (`x`, `y`) VALUES (1, 3)');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->assertNull($this->mm->add($this->e, array(3), NULL));
	}

	public function testEmpty()
	{
		$this->assertNull($this->mm->add($this->e, array(), NULL));
	}

	public function testReturns()
	{
		$this->assertSame(NULL, $this->mm->add($this->e, array(), NULL));
	}

	public function testInverseSide()
	{
		$this->mm->attach(new RelationshipMetaDataManyToMany('foo', 'foo', 'foo', 'foo', NULL, RelationshipMetaDataManyToMany::MAPPED_THERE));
		$this->setExpectedException('Orm\NotSupportedException', 'Orm\IManyToManyMapper::add() has not supported on inverse side.');
		$this->mm->add($this->e, array(), NULL);
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
