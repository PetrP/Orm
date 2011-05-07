<?php

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers Repository::createEntity
 */
class Repository_createEntity_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new Model;
		$this->r = $m->tests;
	}

	public function test()
	{
		$e = $this->r->createEntity(array('id' => 1,'string' => 'xyz'));
		$ee = $this->r->createEntity(array('id' => 1,));
		$this->assertInstanceOf('TestEntity', $e);
		$this->assertSame($ee, $e);
		$this->assertSame('xyz', $ee->string);
	}

	public function testNoId()
	{
		$this->setExpectedException('InvalidStateException', "Data, that is returned from storage, doesn't contain id.");
		$this->r->createEntity(array());
	}

	public function testEmptyId()
	{
		$this->setExpectedException('InvalidStateException', "Data, that is returned from storage, doesn't contain id.");
		$this->r->createEntity(array('id' => ''));
	}

}
