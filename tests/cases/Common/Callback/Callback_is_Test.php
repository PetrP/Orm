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

	public function testNetteCurrentCallback()
	{
		if (class_exists('Nette' . '\\' . 'Callback'))
		{
			$class = 'Nette' . '\\' . 'Callback';
		}
		else if (class_exists('Callback') AND class_exists('Orm' . '\\' . 'Callback'))
		{
			$class = '\Callback';
		}
		else
		{
			$class = 'Callback';
		}
		$this->assertTrue(Callback::is(new $class('foo')));
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testNetteNamespaceCallback()
	{
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestIncomplete('php 5.2 (namespace)');
		}
		if (class_exists('Nette' . '\\' . 'Callback'))
		{
			$this->markTestIncomplete('nette php 5.3');
		}
		eval('name' . 'space Nette; class Call' . 'back {}');
		$c = 'Nette' . '\\' . 'Callback';
		$this->assertTrue(Callback::is(new $c));
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
		eval('class Call' . 'back {}');
		$c = '\Callback';
		$this->assertTrue(Callback::is(new $c));
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testNettePrefixedCallback()
	{
		eval('class NCallback {}');
		$this->assertTrue(Callback::is(new NCallback));
	}

	public function testNot()
	{
		$this->assertFalse(Callback::is(function () {}));
		$this->assertFalse(Callback::is('foo'));
	}

	public function testSeparate()
	{
		if (NETTE_PACKAGE !== '5.2' AND NETTE_PACKAGE !== 'PHP 5.2')
		{
			$this->assertFalse(class_exists('Callback'));
		}
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
