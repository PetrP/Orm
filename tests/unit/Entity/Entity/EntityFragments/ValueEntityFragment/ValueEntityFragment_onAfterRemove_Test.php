<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::onAfterRemove
 */
class ValueEntityFragment_onAfterRemove_Test extends TestCase
{
	private $r;
	private $e;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentity;
		$this->e = $m->testentity->getById(1);
	}

	public function test()
	{
		$e = $this->e;

		$this->assertSame(1, $e->id);
		$this->assertSame('string', $e->string);
		$this->assertSame('TestEntityRepository', get_class($e->repository));
		$this->assertSame(false, $e->isChanged());

		$this->r->remove($e);

		$this->assertSame(NULL, isset($e->id) ? $e->id : NULL);
		$this->assertSame('string', $e->string);
		$this->assertSame(NULL, $e->getRepository(false));
		$this->assertSame(true, $e->isChanged());
	}

}
