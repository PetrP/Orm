<?php

use Orm\InjectionFactory;
use Orm\IEntity;

/**
 * @covers Orm\InjectionFactory::call
 */
class InjectionFactory_call_Test extends TestCase
{

	public function test()
	{
		$callback = InjectionFactory::create(callback(function ($class, IEntity $entity, $value = NULL) {
			return array($class, $entity, $value);
		}), 'class');
		$e = new TestEntity;
		$r = $callback->invoke($e, 'value');
		$this->assertSame(array('class', $e, 'value'), $r);

		$r = $callback->invoke($e, NULL);
		$this->assertSame(array('class', $e, NULL), $r);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\InjectionFactory', 'call');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
