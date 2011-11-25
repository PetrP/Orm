<?php

use Orm\ArrayManyToManyMapper;
use Orm\ManyToMany;
use Orm\RelationshipMetaDataManyToMany;

/**
 * @covers Orm\ArrayManyToManyMapper::attach
 */
class ArrayManyToManyMapper_attach_Test extends TestCase
{
	private $mm;

	protected function setUp()
	{
		$this->mm = new ArrayManyToManyMapper;
	}

	public function testMapped()
	{
		$this->mm->attach(new ManyToMany(new TestEntity, new RelationshipMetaDataManyToMany('TestEntity', 'foo', 'foo', 'foo', 'Orm\ManyToMany', true)));
		$this->assertTrue(true);
	}

	public function testNotMapped()
	{
		$this->setExpectedException('Orm\NotSupportedException', 'Orm\ArrayManyToManyMapper has support only on side where is realtionship mapped.');
		$this->mm->attach(new ManyToMany(new TestEntity, new RelationshipMetaDataManyToMany('TestEntity', 'foo', 'foo', 'foo', 'Orm\ManyToMany', false)));
	}

}
