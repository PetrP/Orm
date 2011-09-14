<?php

use Orm\InjectionFactory;
use Orm\IEntity;
use Orm\Callback;

/**
 * @covers Orm\InjectionFactory::call
 */
class InjectionFactory_call_Test extends TestCase
{

	public function test()
	{
		$use = (object) array();
		$callback = InjectionFactory::create(Callback::create(function ($class, IEntity $entity, $value = NULL) use ($use) {
			$use->data = array($class, $entity, $value);
			return new Injection_create_Injection;
		}), 'class');
		$e = new TestEntity;
		$r = $callback->invoke($e, 'value');
		$this->assertInstanceOf('Injection_create_Injection', $r);
		$this->assertSame(array('class', $e, 'value'), $use->data);

		$r = $callback->invoke($e, NULL);
		$this->assertInstanceOf('Injection_create_Injection', $r);
		$this->assertSame(array('class', $e, NULL), $use->data);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\InjectionFactory', 'call');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function testBad1()
	{
		$callback = InjectionFactory::create(Callback::create(function ($class, IEntity $entity, $value = NULL) {
			return array($class, $entity, $value);
		}), 'class');
		$this->setExpectedException('Orm\BadReturnException', "class factory must return Orm\\IEntityInjection, 'array' given.");
		$callback->invoke(new TestEntity, NULL);
	}

	public function cb()
	{
	}

	public function testBad2()
	{
		$callback = InjectionFactory::create(Callback::create($this, 'cb'), 'class');
		$this->setExpectedException('Orm\BadReturnException', "InjectionFactory_call_Test::cb() must return Orm\\IEntityInjection, 'NULL' given.");
		$callback->invoke(new TestEntity, NULL);
	}
}
