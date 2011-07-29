<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::onPersist
 */
class ValueEntityFragment_onPersist_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentityrepository;
	}

	public function test()
	{
		$e = new TestEntity;
		$e->string = 'xyz';

		$this->assertSame(NULL, isset($e->id) ? $e->id : NULL);
		$this->assertSame('xyz', $e->string);
		$this->assertSame(NULL, $e->getRepository(false));
		$this->assertSame(true, $e->isChanged());

		$this->r->persist($e);

		$this->assertSame(2, $e->id);
		$this->assertSame('xyz', $e->string);
		$this->assertSame('TestEntityRepository', get_class($e->repository));
		$this->assertSame(false, $e->isChanged());
	}

	public function test2()
	{
		$e = new TestEntity;
		$e->___event($e, 'persist', $this->r, 123);
		$this->assertSame(array('id' => 123), $this->readAttribute($e, 'values'));
		$this->assertSame(array('id' => true), $this->readAttribute($e, 'valid'));
		$this->assertSame(false, $e->isChanged());
	}

	public function testBadId()
	{
		$e = new TestEntity;
		$this->setExpectedException('UnexpectedValueException', "Param TestEntity::\$id must be 'id', 'integer' given");
		$e->___event($e, 'persist', $this->r, -1);
	}

}
