<?php

use Orm\RepositoryContainer;


/**
 * @covers Orm\DibiMapper::findAll
 */
class DibiMapper_findAll_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->m = $m->DibiMapper_findAll_Dibi->mapper;
	}

	public function testDibi()
	{
		$all = $this->m->findAll();
		$this->assertInstanceOf('Orm\DibiCollection', $all);
		$this->assertAttributeSame($this->m->connection, 'connection', $all);
		$this->assertAttributeSame($this->m->repository, 'repository', $all);
		$this->assertAttributeSame('dibimapper_findall_dibi', 'tableName', $all);
		$this->assertSame('SELECT `e`.* FROM `dibimapper_findall_dibi` as e', trim(preg_replace('#\s+#', ' ', $all->__toString())));
	}

	public function testDataSource()
	{
		$this->m->collectionClass = 'Orm\DataSourceCollection';
		$all = $this->m->findAll();
		$this->assertInstanceOf('Orm\DataSourceCollection', $all);
		$this->assertSame('SELECT * FROM `dibimapper_findall_dibi`', trim(preg_replace('#\s+#', ' ', $all->__toString())));
	}

	public function testArray()
	{
		$this->m->collectionClass = 'Orm\ArrayCollection';
		$this->setExpectedException('Orm\NotImplementedException');
		$this->m->findAll();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'findAll');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
