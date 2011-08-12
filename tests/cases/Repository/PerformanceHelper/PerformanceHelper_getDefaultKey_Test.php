<?php

use Orm\PerformanceHelper;

/**
 * @covers Orm\PerformanceHelper::getDefaultKey
 */
class PerformanceHelper_getDefaultKey_Test extends TestCase
{

	public function test()
	{
		$k = PerformanceHelper::getDefaultKey();
		$this->assertNotEmpty($k);
		$this->assertSame($_SERVER['REQUEST_URI'], $k);
	}

	public function test2()
	{
		$tmp = $_SERVER['REQUEST_URI'];
		unset($_SERVER['REQUEST_URI']);
		$k = PerformanceHelper::getDefaultKey();
		$this->assertSame(NULL, $k);
		$_SERVER['REQUEST_URI'] = $tmp;
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\PerformanceHelper', 'getDefaultKey');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
