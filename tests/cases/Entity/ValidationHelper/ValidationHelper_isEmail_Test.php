<?php

use Orm\ValidationHelper;

/**
 * @covers Orm\ValidationHelper::isEmail
 */
class ValidationHelper_isEmail_Test extends TestCase
{

	public function test()
	{
		$this->assertTrue(ValidationHelper::isEmail('foo@bar.cz'));
		$this->assertFalse(ValidationHelper::isEmail('foo'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValidationHelper', 'isEmail');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
