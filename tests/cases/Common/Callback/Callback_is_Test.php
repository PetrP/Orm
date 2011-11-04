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
		if (class_exists('Callback'))
		{
			$this->markTestIncomplete('nette php 5.2');
		}
		eval('class Callback {}');
		$c = '\Callback';
		$this->assertTrue(Callback::is(new $c));
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testNettePrefixedCallback()
	{
		eval('class NCallback {}');
		$c = '\NCallback';
		$this->assertTrue(Callback::is(new $c));
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
