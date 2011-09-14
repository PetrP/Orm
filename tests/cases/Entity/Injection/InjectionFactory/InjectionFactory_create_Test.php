<?php

use Orm\InjectionFactory;
use Orm\Callback;

/**
 * @covers Orm\InjectionFactory::create
 * @covers Orm\InjectionFactory::__construct
 */
class InjectionFactory_create_Test extends TestCase
{

	public function test()
	{
		$closure = function () {};
		$callback = InjectionFactory::create(Callback::create($closure), 'class');
		$this->assertInstanceOf('Orm\Callback', $callback);
		$nativeCb = $callback->getNative();
		$this->assertInstanceOf('Orm\InjectionFactory', $nativeCb[0]);
		$this->assertSame('call', $nativeCb[1]);
		$this->assertAttributeSame('class', 'className', $nativeCb[0]);
		$this->assertAttributeSame($closure, 'callback', $nativeCb[0]);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\InjectionFactory', 'create');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
