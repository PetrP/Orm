<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\AttachableEntityFragment::onLoad
 */
class AttachableEntityFragment_onLoad_Test extends TestCase
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
		$this->assertSame(NULL, $e->getRepository(false));
		$e->fireEvent('onLoad', $this->r, array('id' => 1));
		$this->assertSame($this->r, $e->getRepository(false));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AttachableEntityFragment', 'onLoad');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
