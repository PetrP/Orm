<?php

use Orm\Callback;

/**
 * @covers Orm\Callback::invokeArgs
 */
class Callback_invokeArgs_Test extends TestCase
{
	public function cb($a, $b)
	{
		return array(2, $a, $b);
	}

	public function testTwoParams()
	{
		$c = Callback::create($this, 'cb');
		$this->assertSame(array(2, 'a', 'b'), $c->invokeArgs(array('a', 'b')));
	}

	public function testNoCallable()
	{
		$c = Callback::create('Foo', 'cb');
		$this->setExpectedException('Orm\NotCallableException', "Callback 'Foo::cb' is not callable.");
		$c->invokeArgs(array());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Callback', 'invokeArgs');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
