<?php

use Orm\RepositoryContainer;
use Orm\DibiMapper;

abstract class DibiMapper_Connected_Test extends TestCase
{
	/** @var DibiMockExpectedMySqlDriver */
	protected $d;

	/** @var DibiMapper */
	protected $m;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->m = $m->DibiMapper_Connected_Dibi->getMapper();
		$this->d = $this->m->getConnection()->getDriver();
	}

	protected function tearDown()
	{
		$this->d->disconnect();
	}

}

class DibiMapper_Connected_DibiRepository extends TestsRepository
{

}
class DibiMapper_Connected_DibiMapper extends DibiMapper
{
	protected function createConnection()
	{
		return new DibiConnection(array(
			'driver' => 'MockExpectedMySql',
		));
	}
	public function __begin()
	{
		$this->begin();
	}
}
