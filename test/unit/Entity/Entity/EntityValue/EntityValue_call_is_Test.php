<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers _EntityValue::__call
 */
class EntityValue_call_is_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new EntityValue_call_is_Entity;
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
		$this->setExpectedException('MemberAccessException', 'Call to undefined method EntityValue_call_is_Entity::isBbb()');
		$this->e->isBbb();
	}

	public function testNotBool()
	{
		$this->assertTrue($this->e->getCcc());
		$this->setExpectedException('MemberAccessException', 'Call to undefined method EntityValue_call_is_Entity::isCcc()');
		$this->e->isCcc();
	}

	public function testUnexists()
	{
		$this->setExpectedException('MemberAccessException', 'Call to undefined method EntityValue_call_is_Entity::isDdd()');
		$this->e->isDdd();
	}

	public function testUnexists2()
	{
		$this->setExpectedException('MemberAccessException', 'Call to undefined method EntityValue_call_is_Entity::getDdd()');
		$this->e->getDdd();
	}

}