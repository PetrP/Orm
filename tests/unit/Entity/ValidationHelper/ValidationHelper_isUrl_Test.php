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

}
