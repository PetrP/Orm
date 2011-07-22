<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::hydrateEntity
 */
class Repository_hydrateEntity_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->tests;
	}

	public function test()
	{
		$e = $this->r->hydrateEntity(array('id' => 1,'string' => 'xyz'));
		$ee = $this->r->hydrateEntity(array('id' => 1,));
		$this->assertInstanceOf('TestEntity', $e);
		$this->assertSame($ee, $e);
		$this->assertSame('xyz', $ee->string);
	}

	public function testNoId()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Data, that is returned from storage, doesn't contain id.");
		$this->r->hydrateEntity(array());
	}

	public function testEmptyId()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Data, that is returned from storage, doesn't contain id.");
		$this->r->hydrateEntity(array('id' => ''));
	}

}
