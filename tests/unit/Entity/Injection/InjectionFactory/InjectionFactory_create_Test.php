<?php

use Orm\InjectionFactory;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\InjectionFactory::create
 * @covers Orm\InjectionFactory::__construct
 */
class InjectionFactory_create_Test extends TestCase
{

	public function test()
	{
		$closure = function () {};
		$callback = InjectionFactory::create(callback($closure), 'class');
		$this->assertInstanceOf('Nette\Callback', $callback);
		$nativeCb = $callback->getNative();
		$this->assertInstanceOf('Orm\InjectionFactory', $nativeCb[0]);
		$this->assertSame('call', $nativeCb[1]);
		$this->assertAttributeSame('class', 'className', $nativeCb[0]);
		$this->assertAttributeSame($closure, 'callback', $nativeCb[0]);
	}

}
