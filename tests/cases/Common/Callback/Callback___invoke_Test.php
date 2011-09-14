<?php

use Orm\Callback;

/**
 * @covers Orm\Callback::__invoke
 */
class Callback___invoke_Test extends TestCase
{
	public function cb($a, $b)
	{
		return array(2, $a, $b);
	}

	public function testTwoParams()
	{
		$c = Callback::create($this, 'cb');
		$this->assertSame(array(2, 'a', 'b'), $c->__invoke('a', 'b'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Callback', '__invoke');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
