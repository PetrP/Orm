<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::onAttachModel
 */
class ValueEntityFragment_onAttachModel_Test extends TestCase
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
		$e->fireEvent('onAttachModel', NULL, $this->r->model);
		$this->assertAttributeSame($rule, 'rules', $e);
	}

	public function testError()
	{
		$e = new ValueEntityFragment_onAttach_Entity;
		$this->setExpectedException('Orm\MetaDataException', 'fooBar isn\'t repository in ValueEntityFragment_onAttach_Entity::$mixed');
		$e->fireEvent('onAttachModel', NULL, $this->r->model);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', 'onAttachModel');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
