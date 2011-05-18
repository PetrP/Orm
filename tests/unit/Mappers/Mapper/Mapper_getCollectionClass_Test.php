<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

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

	public function testOther()
	{
		$this->m->cc = 'Mapper_getCollectionClass_OtherCollection';
		$this->assertSame('Mapper_getCollectionClass_OtherCollection', $this->m->mockGetCollectionClass());
		$this->assertSame(array('Mapper_getCollectionClass_OtherCollection', NULL), $this->m->mockGetCollectionClass(true));
	}

	public function testNotExists()
	{
		$this->m->cc = 'XyzClassNotExists';
		$this->setExpectedException('Nette\InvalidStateException', "Collection 'XyzClassNotExists' doesn't exists");
		$this->m->mockGetCollectionClass();
	}

	public function testNotCollection()
	{
		$this->m->cc = 'Nette\Utils\Html';
		$this->setExpectedException('Nette\InvalidStateException', "Collection 'Nette\\Utils\\Html' must implement Orm\\IEntityCollection");
		$this->m->mockGetCollectionClass();
	}

	public function testAbstract()
	{
		$this->m->cc = 'Mapper_getCollectionClass_Collection';
		$this->setExpectedException('Nette\InvalidStateException', "Collection 'Mapper_getCollectionClass_Collection' is abstract.");
		$this->m->mockGetCollectionClass();
	}

}
