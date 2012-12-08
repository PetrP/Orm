<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Mapper::getCollectionClass
 */
class Mapper_getCollectionClass_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new Mapper_getCollectionClass_Mapper(new TestsRepository(new RepositoryContainer));
	}

	public function testArray()
	{
		$this->m->cc = 'Orm\ArrayCollection';
		$this->assertSame('Orm\ArrayCollection', $this->m->mockGetCollectionClass());
		$this->assertSame(array('Orm\ArrayCollection', 'array'), $this->m->mockGetCollectionClass(true));
	}

	public function testDibi()
	{
		$this->m->cc = 'Orm\DibiCollection';
		$this->assertSame('Orm\DibiCollection', $this->m->mockGetCollectionClass());
		$this->assertSame(array('Orm\DibiCollection', 'dibi'), $this->m->mockGetCollectionClass(true));
	}

	public function testDataSource()
	{
		$this->m->cc = 'Orm\DataSourceCollection';
		$this->assertSame('Orm\DataSourceCollection', $this->m->mockGetCollectionClass());
		$this->assertSame(array('Orm\DataSourceCollection', 'datasource'), $this->m->mockGetCollectionClass(true));
	}

	public function testSubArray()
	{
		$this->m->cc = 'Mapper_getCollectionClass_ArrayCollection';
		$this->assertSame('Mapper_getCollectionClass_ArrayCollection', $this->m->mockGetCollectionClass());
		$this->assertSame(array('Mapper_getCollectionClass_ArrayCollection', 'array'), $this->m->mockGetCollectionClass(true));
	}

	public function testSubDibi()
	{
		$this->m->cc = 'Mapper_getCollectionClass_DibiCollection';
		$this->assertSame('Mapper_getCollectionClass_DibiCollection', $this->m->mockGetCollectionClass());
		$this->assertSame(array('Mapper_getCollectionClass_DibiCollection', 'dibi'), $this->m->mockGetCollectionClass(true));
	}

	public function testSubDataSource()
	{
		$this->m->cc = 'Mapper_getCollectionClass_DataSourceCollection';
		$this->assertSame('Mapper_getCollectionClass_DataSourceCollection', $this->m->mockGetCollectionClass());
		$this->assertSame(array('Mapper_getCollectionClass_DataSourceCollection', 'datasource'), $this->m->mockGetCollectionClass(true));
	}

	public function testAbsoluteNamespace()
	{
		$this->m->cc = '\Orm\ArrayCollection';
		$this->assertSame(array('Orm\ArrayCollection', 'array'), $this->m->mockGetCollectionClass(true));
		$this->assertSame('Orm\ArrayCollection', $this->m->mockGetCollectionClass());
	}

	public function testSubAbsoluteNamespace()
	{
		$this->m->cc = '\Mapper_getCollectionClass_ArrayCollection';
		$this->assertSame(array('Mapper_getCollectionClass_ArrayCollection', 'array'), $this->m->mockGetCollectionClass(true));
		$this->assertSame('Mapper_getCollectionClass_ArrayCollection', $this->m->mockGetCollectionClass());
	}

	public function testCaseInsensitive()
	{
		$this->m->cc = 'Orm\ARRAYcollection';
		$this->assertSame(array('Orm\ArrayCollection', 'array'), $this->m->mockGetCollectionClass(true));
		$this->assertSame('Orm\ArrayCollection', $this->m->mockGetCollectionClass());
	}

	public function testSubCaseInsensitive()
	{
		$this->m->cc = 'MAPPER_getCollectionClass_arrayCollection';
		$this->assertSame(array('Mapper_getCollectionClass_ArrayCollection', 'array'), $this->m->mockGetCollectionClass(true));
		$this->assertSame('Mapper_getCollectionClass_ArrayCollection', $this->m->mockGetCollectionClass());
	}

	public function testOther()
	{
		$this->m->cc = 'Mapper_getCollectionClass_OtherCollection';
		$this->assertSame('Mapper_getCollectionClass_OtherCollection', $this->m->mockGetCollectionClass());
		$this->assertSame(array('Mapper_getCollectionClass_OtherCollection', NULL), $this->m->mockGetCollectionClass(true));
	}

	public function testNotExists()
	{
		$this->m->cc = 'XyzClassNotExists';
		$this->setExpectedException('Orm\BadReturnException', "Mapper_getCollectionClass_Mapper::createCollectionClass() must return Orm\\IEntityCollection class name; 'XyzClassNotExists' doesn't exists");
		$this->m->mockGetCollectionClass();
	}

	public function testNotCollection()
	{
		$this->m->cc = 'Directory';
		$this->setExpectedException('Orm\BadReturnException', "Mapper_getCollectionClass_Mapper::createCollectionClass() must return Orm\\IEntityCollection class name; 'Directory' must implement Orm\\IEntityCollection");
		$this->m->mockGetCollectionClass();
	}

	public function testAbstract()
	{
		$this->m->cc = 'Mapper_getCollectionClass_Collection';
		$this->setExpectedException('Orm\BadReturnException', "Mapper_getCollectionClass_Mapper::createCollectionClass() must return Orm\\IEntityCollection class name; 'Mapper_getCollectionClass_Collection' is abstract.");
		$this->m->mockGetCollectionClass();
	}

	public function testNotInstantiable()
	{
		$this->m->cc = 'Mapper_getCollectionClass_BadCollection';
		$this->setExpectedException('Orm\BadReturnException', "Mapper_getCollectionClass_Mapper::createCollectionClass() must return Orm\\IEntityCollection class name; 'Mapper_getCollectionClass_BadCollection' isn't instantiable");
		$this->m->mockGetCollectionClass();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Mapper', 'getCollectionClass');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
