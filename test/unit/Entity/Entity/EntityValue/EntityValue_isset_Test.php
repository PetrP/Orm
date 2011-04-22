<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers _EntityValue::__isset
 */
class EntityValue_isset_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new EntityValue_isset_Entity;
	}

	public function testBase()
	{
		$this->e->test = 'ok';
		$this->assertTrue(isset($this->e->test));
	}

	public function testUnknown()
	{
		$this->assertFalse(isset($this->e->unexist));
	}

	public function testNull()
	{
		$this->e->test = NULL;
		$this->assertFalse(isset($this->e->test));
	}

	public function testException()
	{
		$this->assertFalse(isset($this->e->test2));
	}

}
