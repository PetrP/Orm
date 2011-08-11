<?php

use Orm\ValidationHelper;

/**
 * @covers Orm\ValidationHelper::isUrl
 */
class ValidationHelper_isUrl_Test extends TestCase
{

	public function test()
	{
		$this->assertTrue(ValidationHelper::isUrl('http://google.com'));
		$this->assertTrue(ValidationHelper::isUrl('google.com'));
		$this->assertTrue(ValidationHelper::isUrl('http://google.com/foo/bar'));
		$this->assertFalse(ValidationHelper::isUrl('foo'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValidationHelper', 'isUrl');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
