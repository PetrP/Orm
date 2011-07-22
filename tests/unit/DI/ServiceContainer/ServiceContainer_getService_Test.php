<?php

use Orm\ServiceContainer;
use Nette\Utils\Html;

/**
 * @covers Orm\ServiceContainer::getService
 */
class ServiceContainer_getService_Test extends TestCase
{
	private $c;

	protected function setUp()
	{
		$this->c = new ServiceContainer;
	}

	public function testNot()
	{
		$this->setExpectedException('Orm\ServiceNotFoundException', "Service 'test' not found");
		$this->c->getService('test');
	}

	public function testObject()
	{
		$h = new Html;
		$this->c->addService('test', $h);
		$this->assertSame($h, $this->c->getService('test'));
		$this->assertSame($h, $this->c->getService('test'));
	}

	public function testClassName()
	{
		$this->c->addService('test', 'Nette\Utils\Html');
		$h = $this->c->getService('test');
		$this->assertInstanceOf('Nette\Utils\Html', $h);
		$this->assertSame($h, $this->c->getService('test'));
	}

	public function testClosure()
	{
		$this->c->addService('test', function () { return new Nette\Utils\Html; });
		$h = $this->c->getService('test');
		$this->assertInstanceOf('Nette\Utils\Html', $h);
		$this->assertSame($h, $this->c->getService('test'));
	}

	public function testCreateFunction()
	{
		$this->c->addService('test', create_function('', 'return new Nette\Utils\Html;'));
		$h = $this->c->getService('test');
		$this->assertInstanceOf('Nette\Utils\Html', $h);
		$this->assertSame($h, $this->c->getService('test'));
	}

	public static function callback()
	{
		return new Html;
	}

	public function testStringCallback()
	{
		$this->c->addService('test', __CLASS__ . '::callback');
		$h = $this->c->getService('test');
		$this->assertInstanceOf('Nette\Utils\Html', $h);
		$this->assertSame($h, $this->c->getService('test'));
	}

	public function testArrayCallback()
	{
		$this->c->addService('test', array(__CLASS__, 'callback'));
		$h = $this->c->getService('test');
		$this->assertInstanceOf('Nette\Utils\Html', $h);
		$this->assertSame($h, $this->c->getService('test'));
	}

	public function testNetteCallback()
	{
		$this->c->addService('test', callback($this, 'callback'));
		$h = $this->c->getService('test');
		$this->assertInstanceOf('Nette\Utils\Html', $h);
		$this->assertSame($h, $this->c->getService('test'));
	}

	public function testInvalidInt()
	{
		$this->c->addService('test', 123);
		$this->setExpectedException('Orm\InvalidServiceFactoryException', "Service 'test' has invalid factory. Callback, class name or object expected, integer given.");
		$this->c->getService('test');
	}

	public function testInvalidCallbackReturn()
	{
		$this->c->addService('test', function () { return 123; });
		$this->setExpectedException('Orm\InvalidServiceFactoryException', "Factory for service 'test' returns invalid result. Object expected, integer given.");
		$this->c->getService('test');
	}

	public function testInstanceOf()
	{
		$this->c->addService('test', 'Nette\Utils\Html');
		$this->assertInstanceOf('Nette\Utils\Html', $x1 = $this->c->getService('test', 'Nette\Utils\Html'));
		$this->assertInstanceOf('Nette\Utils\Html', $x2 = $this->c->getService('test', 'Countable'));
		$this->assertSame($x1, $x2);
	}

	public function testInstanceOfBad()
	{
		$this->c->addService('test', 'Nette\Utils\Html');
		$this->setExpectedException('Orm\ServiceNotInstanceOfException', "Service 'test' is not instance of 'Orm\\IMapperFactory'.");
		$this->c->getService('test', 'Orm\IMapperFactory');
	}

}
