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
		$this->mm->attach($m = new RelationshipMetaDataManyToMany('TestEntity', 'foo', 'foo', 'foo', 'Orm\ManyToMany', true));
		$this->assertAttributeSame($m, 'meta', $this->mm);
	}

	public function testNotMapped()
	{
		$this->mm->attach($m = new RelationshipMetaDataManyToMany('TestEntity', 'foo', 'foo', 'foo', 'Orm\ManyToMany', false));
		$this->assertAttributeSame($m, 'meta', $this->mm);
	}

}
