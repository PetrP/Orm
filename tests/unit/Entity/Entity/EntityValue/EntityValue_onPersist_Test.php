<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers _EntityValue::onPersist
 */
class EntityValue_onPersist_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentity;
	}

	public function test()
	{
		$e = new TestEntity;
		$e->string = 'xyz';

		$this->assertSame(NULL, isset($e->id) ? $e->id : NULL);
		$this->assertSame('xyz', $e->string);
		$this->assertSame(NULL, $e->getGeneratingRepository(false));
		$this->assertSame(true, $e->isChanged());

		$this->r->persist($e);

		$this->assertSame(2, $e->id);
		$this->assertSame('xyz', $e->string);
		$this->assertSame('testentity', $e->generatingRepository->repositoryName);
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
