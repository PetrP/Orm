<?php

use Orm\ArrayManyToManyMapper;
use Orm\RelationshipMetaDataManyToMany;

/**
 * @covers Orm\ArrayManyToManyMapper::remove
 */
class ArrayManyToManyMapper_remove_Test extends TestCase
{
	private $mm;

	protected function setUp()
	{
		$this->mm = new ArrayManyToManyMapper;
	}

	public function testInverseSide()
	{
		$this->mm->attach(new RelationshipMetaDataManyToMany('foo', 'foo', 'foo', 'foo', NULL, RelationshipMetaDataManyToMany::MAPPED_THERE));
		$this->setExpectedException('Orm\NotSupportedException', 'Orm\IManyToManyMapper::remove() has not supported on inverse side.');
		$this->mm->remove(new TestEntity, array(), NULL);
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
