<?php

/**
 * @covers Orm\DibiPersistenceHelper::getMapper
 * @covers Orm\DibiPersistenceHelper::setMapper
 */
class DibiPersistenceHelper_mapper_Test extends DibiPersistenceHelper_Test
{

	public function testGet()
	{
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($this->h->connection, $this->h->conventional, 'table');

		$this->setExpectedException('Nette\DeprecatedException', 'Orm\DibiPersistenceHelper::$mapper is depreacted');
		$h->mapper;
	}

	public function testSet()
	{
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($this->h->connection, $this->h->conventional, 'table');

		$this->setExpectedException('Nette\DeprecatedException', 'Orm\DibiPersistenceHelper::$mapper is depreacted');
		$h->mapper = 'x';
	}

}
