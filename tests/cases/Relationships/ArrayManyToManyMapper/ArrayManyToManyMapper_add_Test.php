<?php

use Orm\ArrayManyToManyMapper;
use Orm\RelationshipMetaDataManyToMany;

/**
 * @covers Orm\ArrayManyToManyMapper::add
 */
class ArrayManyToManyMapper_add_Test extends TestCase
{
	private $mm;

	protected function setUp()
	{
		$this->mm = new ArrayManyToManyMapper;
	}

	public function testInverseSide()
	{
		$this->mm->attach(new RelationshipMetaDataManyToMany('foo', 'foo', 'foo', 'foo', NULL, RelationshipMetaDataManyToMany::MAPPED_THERE));
		$this->setExpectedException('Orm\NotSupportedException', 'Orm\IManyToManyMapper::add() has not supported on inverse side.');
		$this->mm->add(new TestEntity, array(), NULL);
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
