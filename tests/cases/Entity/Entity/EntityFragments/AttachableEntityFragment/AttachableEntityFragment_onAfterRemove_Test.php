<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\AttachableEntityFragment::onAfterRemove
 */
class AttachableEntityFragment_onAfterRemove_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentityrepository;
	}

	public function test()
	{
		$e = $this->r->getById(1);
		$this->assertSame($this->r, $e->getRepository(false));
		$this->r->remove($e);
		$this->assertSame(NULL, $e->getRepository(false));
	}

	public function testErrorAfterRemove()
	{
		$e = $this->r->getById(1);;
		$this->r->remove($e);
		$this->setExpectedException('Orm\EntityWasRemovedException', 'TestEntity was removed. Clone entity before reattach to repository.');
		$this->r->attach($e);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AttachableEntityFragment', 'onAfterRemove');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
