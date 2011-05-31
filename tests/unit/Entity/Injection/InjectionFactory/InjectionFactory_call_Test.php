<?php

use Orm\InjectionFactory;
use Orm\IEntity;

require_once dirname(__FILE__) . '/../../../../boot.php';

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

		$r = $callback->invoke($e);
		$this->assertSame(array('class', $e, NULL), $r);
	}

}
