<?php

/**
 * @covers Orm\ValueEntityFragment::__isset
 */
class ValueEntityFragment_isset_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new ValueEntityFragment_isset_Entity;
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

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', '__isset');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
