<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::onAttach
 */
class ValueEntityFragment_onAttach_Test extends TestCase
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
		$rule = $this->readAttribute($e, 'rules');
		$e->fireEvent('onAttach', $this->r);
		$this->assertAttributeSame($rule, 'rules', $e);
	}

	public function testError()
	{
		$e = new ValueEntityFragment_onAttach_Entity;
		$this->setExpectedException('Orm\MetaDataException', 'fooBar isn\'t repository in ValueEntityFragment_onAttach_Entity::$mixed');
		$e->fireEvent('onAttach', $this->r);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', 'onAttach');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
