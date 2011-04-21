<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers _EntityValue::onPersist
 */
class EntityValue_onPersist_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new Model;
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

}
