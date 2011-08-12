<?php

use Orm\DibiManyToManyMapper;
use Orm\RepositoryContainer;

abstract class DibiManyToManyMapper_Connected_Test extends TestCase
{
	/** @var DibiMockExpectedMySqlDriver */
	protected $d;

	/** @var DibiManyToManyMapper */
	protected $mm;

	/** @var TestEntity */
	protected $e;

	protected function setUp()
	{
		$c = new DibiConnection(array(
			'driver' => 'MockExpectedMySql',
		));
		$this->mm = new DibiManyToManyMapper($c);
		$this->mm->parentParam = 'x';
		$this->mm->childParam = 'y';
		$this->mm->table = 't';
		$this->d = $c->getDriver();
		$r = new TestsRepository(new RepositoryContainer);
		$this->e = $r->getById(1);
	}

	protected function tearDown()
	{
		$this->d->disconnect();
	}

}
