<?php

use Orm\DibiManyToManyMapper;
use Orm\RepositoryContainer;
use Orm\RelationshipMetaDataToMany;
use Orm\RelationshipMetaDataManyToMany;

/**
 * @covers Orm\DibiManyToManyMapper::attach
 */
class DibiManyToManyMapper_attach_Test extends TestCase
{
	private $mm;
	private $m;
	private $m2;
	private $m3;

	protected function setUp()
	{
		$c = new DibiConnection(array('lazy' => true));
		$this->mm = new DibiManyToManyMapper($c);
		$this->m = new RelationshipMetaDataManyToMany('TestEntity', 'bar', 'TestsRepository', 'foo', NULL, RelationshipMetaDataToMany::MAPPED_HERE);
		$this->m2 = new RelationshipMetaDataManyToMany('TestEntity', 'bar', 'TestsRepository', 'foo', NULL, RelationshipMetaDataToMany::MAPPED_THERE);
		$this->m3 = new MockRelationshipMetaDataManyToManyBoth('TestEntity', 'bar', 'TestsRepository', 'foo');
	}

	public function test()
	{
		$this->mm->parentParam = 'x';
		$this->mm->childParam = 'y';
		$this->mm->table = 't';
		$this->mm->attach($this->m);
		$this->assertTrue(true);
		$this->assertSame('x', $this->mm->parentParam);
		$this->assertSame('y', $this->mm->childParam);
		$this->assertSame('t', $this->mm->table);
		$this->assertAttributeSame(RelationshipMetaDataToMany::MAPPED_HERE, 'mapped', $this->mm);
	}

	public function testNoChildParam()
	{
		$this->mm->parentParam = 'y';
		$this->mm->table = 't';
		$this->setExpectedException('Orm\RequiredArgumentException', 'Orm\DibiManyToManyMapper::$childParam is required');
		$this->mm->attach($this->m);
	}

	public function testNoParentParam()
	{
		$this->mm->childParam = 'x';
		$this->mm->table = 't';
		$this->setExpectedException('Orm\RequiredArgumentException', 'Orm\DibiManyToManyMapper::$parentParam is required');
		$this->mm->attach($this->m);
	}

	public function testNoTable()
	{
		$this->mm->childParam = 'x';
		$this->mm->parentParam = 'y';
		$this->setExpectedException('Orm\RequiredArgumentException', 'Orm\DibiManyToManyMapper::$table is required');
		$this->mm->attach($this->m);
	}

	public function testNotMappetByParent()
	{
		$this->mm->parentParam = 'x';
		$this->mm->childParam = 'y';
		$this->mm->table = 't';
		$this->mm->attach($this->m2);
		$this->assertSame('y', $this->mm->parentParam);
		$this->assertSame('x', $this->mm->childParam);
		$this->assertSame('t', $this->mm->table);
		$this->assertAttributeSame(RelationshipMetaDataToMany::MAPPED_THERE, 'mapped', $this->mm);
	}

	public function testNoChildParamNotMappetByParent()
	{
		$this->mm->parentParam = 'y';
		$this->mm->table = 't';
		$this->setExpectedException('Orm\RequiredArgumentException', 'Orm\DibiManyToManyMapper::$childParam is required');
		$this->mm->attach($this->m2);
	}

	public function testNoParentParamNotMappetByParent()
	{
		$this->mm->childParam = 'x';
		$this->mm->table = 't';
		$this->setExpectedException('Orm\RequiredArgumentException', 'Orm\DibiManyToManyMapper::$parentParam is required');
		$this->mm->attach($this->m2);
	}

	public function testNoTableNotMappetByParent()
	{
		$this->mm->childParam = 'x';
		$this->mm->parentParam = 'y';
		$this->setExpectedException('Orm\RequiredArgumentException', 'Orm\DibiManyToManyMapper::$table is required');
		$this->mm->attach($this->m2);
	}

	public function testBoth()
	{
		$this->mm->parentParam = 'x';
		$this->mm->childParam = 'y';
		$this->mm->table = 't';
		$this->mm->attach($this->m3);
		$this->assertSame('x', $this->mm->parentParam);
		$this->assertSame('y', $this->mm->childParam);
		$this->assertSame('t', $this->mm->table);
		$this->assertAttributeSame(RelationshipMetaDataToMany::MAPPED_BOTH, 'mapped', $this->mm);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiManyToManyMapper', 'attach');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
