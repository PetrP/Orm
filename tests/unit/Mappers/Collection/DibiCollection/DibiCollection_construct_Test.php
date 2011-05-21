<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DibiCollection::__construct
 */
class DibiCollection_construct_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->assertAttributeSame('dibicollection', 'tableName', $this->c);
		$this->assertAttributeSame($this->m->repository, 'repository', $this->c);
		$this->assertAttributeSame($this->m->connection, 'connection', $this->c);
	}

	public function testSql()
	{
		$this->a('SELECT [e].* FROM [dibicollection] as e');
	}

}
