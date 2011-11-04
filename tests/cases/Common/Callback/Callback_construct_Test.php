<?php

use Orm\Callback;

/**
 * @covers Orm\Callback::__construct
 */
class Callback_construct_Test extends TestCase
{
	public function testTwoParams()
	{
		$c = Callback::create($this, 'test');
		$this->assertSame(array($this, 'test'), $c->getNative());
	}

	public function testArray()
	{
		$c = Callback::create(array($this, 'test'));
		$this->assertSame(array($this, 'test'), $c->getNative());
	}

	public function testFunction()
	{
		$c = Callback::create('print_r');
		$this->assertSame('print_r', $c->getNative());
	}

	public function testStatic()
	{
		$c = Callback::create('Callback_construct_Test::staticCB');
		$this->assertSame(array('Callback_construct_Test', 'staticCB'), $c->getNative());
	}

	public static function staticCB()
	{

	}

	public function testClosure()
	{
		$f = function () {};
		$c = Callback::create($f);
		$this->assertSame($f, $c->getNative());
	}

	public function testLambda()
	{
		$f = create_function('', '');
		$c = Callback::create($f);
		$this->assertSame($f, $c->getNative());
	}

	public function testInvoke()
	{
		$c = Callback::create($this);
		$this->assertSame(array($this, '__invoke'), $c->getNative());
	}

	public function testCallback()
	{
		if (PHP_VERSION_ID < 50300)
		{
			throw new PHPUnit_Framework_IncompleteTestError('php 5.2 (setAccessible)');
		}
		if (PHP_VERSION_ID < 50302)
		{
			throw new PHPUnit_Framework_IncompleteTestError('php < 5.3.2 (ReflectionMethod::setAccessible)');
		}
		$c1 = Callback::create($this, 'test');
		$c2 = Callback::create('foo');
		$r = new ReflectionMethod('Orm\Callback', '__construct');
		$r->setAccessible(true);
		$r->invoke($c2, $c1); // vola znovu constroctor
		$this->assertSame(array($this, 'test'), $c2->getNative());
		$this->assertNotSame($c1, $c2);
	}

	public function testNetteCallback()
	{
		$nc = callback($this, 'test');
		$c = Callback::create($nc);
		$this->assertInstanceOf('Nette\Callback', $nc);
		$this->assertInstanceOf('Orm\Callback', $c);
		$this->assertSame(array($this, 'test'), $c->getNative());
	}

	public function testInvalid()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', 'Invalid callback.');
		Callback::create(array('foo', 'bar', 'foo'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Callback', '__construct');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
