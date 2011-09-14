<?php

use Orm\Callback;

/**
 * @covers Orm\Callback::is
 */
class Callback_is_Test extends TestCase
{

	public function testCallback()
	{
		$this->assertTrue(Callback::is(Callback::create('foo')));
	}

	public function testNetteNamespaceCallback()
	{
		$this->assertTrue(Callback::is(new Nette\Callback('foo')));
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testNetteNotNamespaceCallback()
	{
		eval('class Callback {}');
		$this->assertTrue(Callback::is(new \Callback));
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testNettePrefixedCallback()
	{
		eval('class NCallback {}');
		$this->assertTrue(Callback::is(new \NCallback('foo')));
	}

	public function testNot()
	{
		$this->assertFalse(Callback::is(function () {}));
		$this->assertFalse(Callback::is('foo'));
	}

	public function testSeparate()
	{
		$this->assertFalse(class_exists('Callback'));
		$this->assertFalse(class_exists('NCallback'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Callback', 'is');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
