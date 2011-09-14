<?php

/**
 * @covers Orm\ValueEntityFragment::__call
 */
class ValueEntityFragment_call_is_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new ValueEntityFragment_call_is_Entity;
	}

	public function testOk()
	{
		$this->assertTrue($this->e->isAaa());
		$this->assertTrue($this->e->getAaa());
		$this->e->aaa = false;
		$this->assertFalse($this->e->isAaa());
		$this->assertFalse($this->e->getAaa());
	}

	public function testMoreType()
	{
		$this->assertTrue($this->e->getBbb());
		$this->setExpectedException('Orm\MemberAccessException', 'Call to undefined method ValueEntityFragment_call_is_Entity::isBbb()');
		$this->e->isBbb();
	}

	public function testNotBool()
	{
		$this->assertTrue($this->e->getCcc());
		$this->setExpectedException('Orm\MemberAccessException', 'Call to undefined method ValueEntityFragment_call_is_Entity::isCcc()');
		$this->e->isCcc();
	}

	public function testUnexists()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Call to undefined method ValueEntityFragment_call_is_Entity::isDdd()');
		$this->e->isDdd();
	}

	public function testUnexists2()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Call to undefined method ValueEntityFragment_call_is_Entity::getDdd()');
		$this->e->getDdd();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', '__call');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
