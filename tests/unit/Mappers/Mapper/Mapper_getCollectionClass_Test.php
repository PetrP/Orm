<?php

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers Mapper::getCollectionClass
 */
class Mapper_getCollectionClass_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new Mapper_getCollectionClass_Mapper(new TestsRepository(new Model));
	}

	public function testArray()
	{
		$this->m->cc = 'ArrayCollection';
		$this->assertSame('ArrayCollection', $this->m->mockGetCollectionClass());
		$this->assertSame(array('ArrayCollection', 'array'), $this->m->mockGetCollectionClass(true));
	}

	public function testDibi()
	{
		$this->m->cc = 'DibiCollection';
		$this->assertSame('DibiCollection', $this->m->mockGetCollectionClass());
		$this->assertSame(array('DibiCollection', 'dibi'), $this->m->mockGetCollectionClass(true));
	}

	public function testDataSource()
	{
		$this->m->cc = 'DataSourceCollection';
		$this->assertSame('DataSourceCollection', $this->m->mockGetCollectionClass());
		$this->assertSame(array('DataSourceCollection', 'datasource'), $this->m->mockGetCollectionClass(true));
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

	public function testOther()
	{
		$this->m->cc = 'Mapper_getCollectionClass_OtherCollection';
		$this->assertSame('Mapper_getCollectionClass_OtherCollection', $this->m->mockGetCollectionClass());
		$this->assertSame(array('Mapper_getCollectionClass_OtherCollection', NULL), $this->m->mockGetCollectionClass(true));
	}

	public function testNotExists()
	{
		$this->m->cc = 'XyzClassNotExists';
		$this->setExpectedException('InvalidStateException', "Collection 'XyzClassNotExists' doesn't exists");
		$this->m->mockGetCollectionClass();
	}

	public function testNotCollection()
	{
		$this->m->cc = 'Html';
		$this->setExpectedException('InvalidStateException', "Collection 'Html' must implement IEntityCollection");
		$this->m->mockGetCollectionClass();
	}

	public function testAbstract()
	{
		$this->m->cc = 'Mapper_getCollectionClass_Collection';
		$this->setExpectedException('InvalidStateException', "Collection 'Mapper_getCollectionClass_Collection' is abstract.");
		$this->m->mockGetCollectionClass();
	}

}
