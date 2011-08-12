<?php

use Orm\InjectionFactory;
use Orm\IEntity;

/**
 * @covers Orm\Injection::getInjectedValue
 * @covers Orm\Injection::setInjectedValue
 */
class Injection_getSetInjectedValue_Test extends TestCase
{

	public function test()
	{
		$i = new Injection_create_Injection;
		$this->assertAttributeSame(NULL, 'value', $i);
		$this->assertSame(NULL, $i->getInjectedValue());
		$this->assertSame($i, $i->setInjectedValue('foo'));
		$this->assertAttributeSame('foo', 'value', $i);
		$this->assertSame('foo', $i->getInjectedValue());
	}

}
