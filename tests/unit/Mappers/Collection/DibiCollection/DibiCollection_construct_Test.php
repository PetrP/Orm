<?php

use Orm\DibiCollection;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DibiCollection::__construct
 * @covers Orm\BaseDibiCollection::__construct
 */
class DibiCollection_construct_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->assertAttributeSame('dibicollection', 'tableName', $this->c);
		$this->assertAttributeSame($this->m->repository, 'repository', $this->c);
		$this->assertAttributeSame($this->m->connection, 'connection', $this->c);
		$this->assertAttributeSame($this->m->repository->mapper->conventional, 'conventional', $this->c);
	}

	public function testSql()
	{
		$this->a('SELECT `e`.* FROM `dibicollection` as e');
	}

	public function testAscDesc()
	{
		$this->assertSame(Dibi::DESC, DibiCollection::DESC);
		$this->assertSame(Dibi::ASC, DibiCollection::ASC);
	}

}
