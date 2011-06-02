<?php

use Orm\MetaData;
use Orm\MetaDataProperty;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\MetaDataProperty::setInjection
 */
class MetaDataProperty_setInjection_Test extends TestCase
{
	private $m;
	private $p;

	protected function setUp()
	{
		$this->m = new MetaData('MetaData_Test_Entity');
		$this->p = new MetaDataProperty($this->m, 'id', 'MetaDataProperty_setInjection_Injection');
	}

	private function t($class, $callback)
	{
		$a = $this->p->toArray();
		$this->assertInstanceOf('Nette\Callback', $a['injection']);
		$cb = $a['injection']->getNative();
		$this->assertInstanceOf('Orm\InjectionFactory', $cb[0]);
		$this->assertSame('call', $cb[1]);
		$this->assertAttributeSame($class, 'className', $cb[0]);
		$this->assertAttributeSame($callback, 'callback', $cb[0]);
	}

	public function testTwice()
	{
		$this->p->setInjection();
		$this->setExpectedException('Nette\InvalidStateException', 'Already has injection in MetaData_Test_Entity::$id');
		$this->p->setInjection();
	}

	public function testMoreType()
	{
		$this->p = new MetaDataProperty($this->m, 'id', 'string|int');
		$this->setExpectedException('Nette\InvalidStateException', "Injection expecte type as one class implements Orm\\IInjection, 'string|int' given in MetaData_Test_Entity::\$id");
		$this->p->setInjection();
	}

	public function testNotClass()
	{
		$this->p = new MetaDataProperty($this->m, 'id', 'FooBar');
		$this->setExpectedException('Nette\InvalidStateException', "Injection expecte type as class implements Orm\\IInjection, 'FooBar' given in MetaData_Test_Entity::\$id");
		$this->p->setInjection();
	}

	public function testClassNotImplement()
	{
		$this->p = new MetaDataProperty($this->m, 'id', 'Nette\Utils\Html');
		$this->setExpectedException('Nette\InvalidStateException', "Nette\\Utils\\Html does not implements Orm\\IEntityInjection in MetaData_Test_Entity::\$id");
		$this->p->setInjection();
	}

	public function testClassNotInstantiable()
	{
		$this->p = new MetaDataProperty($this->m, 'id', 'Orm\Injection');
		$this->setExpectedException('Nette\InvalidStateException', "Orm\\Injection is abstract or not instantiable in MetaData_Test_Entity::\$id");
		$this->p->setInjection();
	}

	public function testStaticLoader()
	{
		$this->p->setInjection();
		$this->t('MetaDataProperty_setInjection_Injection', array('MetaDataProperty_setInjection_Injection', 'create'));
	}

	public function testJustInjection()
	{
		$this->p = new MetaDataProperty($this->m, 'id', 'MetaDataProperty_setInjection_JustInjection');
		$this->setExpectedException('Nette\InvalidStateException', "There is not factory callback for injection in MetaData_Test_Entity::\$id, specify one or use Orm\\IEntityInjectionStaticLoader");
		$this->p->setInjection();
	}

	public function testCallbackLoader()
	{
		$this->p = new MetaDataProperty($this->m, 'id', 'MetaDataProperty_setInjection_JustInjection');
		$loader = new MetaDataProperty_setInjection_NonStaticInjectionLoader;
		$this->p->setInjection($loader);
		$this->t('MetaDataProperty_setInjection_JustInjection', array($loader, 'create'));
	}

	public function testCallbackCallback()
	{
		$this->p = new MetaDataProperty($this->m, 'id', 'MetaDataProperty_setInjection_JustInjection');
		$loader = callback(function () {});
		$this->p->setInjection($loader);
		$this->t('MetaDataProperty_setInjection_JustInjection', $loader->getNative());
	}

	public function testCallbackClosure()
	{
		$this->p = new MetaDataProperty($this->m, 'id', 'MetaDataProperty_setInjection_JustInjection');
		$loader = function () {};
		$this->p->setInjection($loader);
		$this->t('MetaDataProperty_setInjection_JustInjection', $loader);
	}

	public function testCallbackString()
	{
		$this->p = new MetaDataProperty($this->m, 'id', 'MetaDataProperty_setInjection_JustInjection');
		$this->p->setInjection('MetaDataProperty_setInjection_NonStaticInjectionLoader::create');
		$this->t('MetaDataProperty_setInjection_JustInjection', array('MetaDataProperty_setInjection_NonStaticInjectionLoader', 'create'));
	}

	public function testCallbackBad()
	{
		$this->p = new MetaDataProperty($this->m, 'id', 'MetaDataProperty_setInjection_JustInjection');
		$this->setExpectedException('Nette\InvalidStateException', "Injection expected valid callback, 'xyz' given in MetaData_Test_Entity::\$id, specify one or use Orm\\IEntityInjectionStaticLoader");
		$this->p->setInjection('xyz');
	}

	public function testReturns()
	{
		$this->assertSame($this->p, $this->p->setInjection());
	}

}
