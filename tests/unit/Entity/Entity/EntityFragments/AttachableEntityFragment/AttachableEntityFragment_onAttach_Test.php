<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\AttachableEntityFragment::onAttach
 */
class AttachableEntityFragment_onAttach_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentityrepository;
	}

	public function testNew()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $e->getRepository(false));
		$this->r->attach($e);
		$this->assertSame($this->r, $e->getRepository(false));
	}

	public function testAlreadyAttached()
	{
		$e = $this->r->getById(1);
		$this->assertSame($this->r, $e->getRepository());
		$this->r->attach($e);
		$this->assertSame($this->r, $e->getRepository());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AttachableEntityFragment', 'onAttach');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
