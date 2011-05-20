<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\DibiMapper::dataSource
 */
class DibiMapper_dataSource_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new DibiMapper_dataSource_DibiMapper(new TestsRepository(new RepositoryContainer));
	}

	public function test()
	{
		$ds = $this->m->ds('table');
		$this->assertInstanceOf('Orm\DataSourceCollection', $ds);
		$this->assertTrue($this->m->connection->isConnected());
		$this->assertAttributeSame($this->m->connection, 'connection', $ds);
		$this->assertAttributeSame($this->m->repository, 'repository', $ds);
		$this->assertAttributeSame('SELECT * FROM [table]', 'sql', $ds);
	}

	public function test2()
	{
		$ds = $this->m->ds('SELECT * FROM %n WHERE %n = %i', 'table', 'foo' , 5);
		$this->assertAttributeSame('SELECT * FROM [table] WHERE [foo] = 5', 'sql', $ds);
	}

	public function testDibiCollection()
	{
		$this->m->collectionClass = 'Orm\DibiCollection';
		$this->assertInstanceOf('Orm\DataSourceCollection', $this->m->ds('table'));
	}

	public function testDibiCollectionCustom()
	{
		$this->m->collectionClass = 'DibiMapper_dataSource_DibiCollection';
		$this->setExpectedException('Nette\NotSupportedException');
		$this->m->ds('table');
	}

	public function testArrayCollection()
	{
		$this->m->collectionClass = 'Orm\ArrayCollection';
		$this->setExpectedException('Nette\NotSupportedException');
		$this->m->ds('table');
	}

	public function testArrayCollectionCustom()
	{
		$this->m->collectionClass = 'DibiMapper_dataSource_ArrayCollection';
		$this->setExpectedException('Nette\NotSupportedException');
		$this->m->ds('table');
	}

	public function testDataSourceCollection()
	{
		$this->m->collectionClass = 'Orm\DataSourceCollection';
		$this->assertInstanceOf('Orm\DataSourceCollection', $this->m->ds('table'));
	}

	public function testDataSourceCollectionCustom()
	{
		$this->m->collectionClass = 'DibiMapper_dataSource_DataSourceCollection';
		$this->assertInstanceOf('Orm\DataSourceCollection', $this->m->ds('table'));
	}

}
