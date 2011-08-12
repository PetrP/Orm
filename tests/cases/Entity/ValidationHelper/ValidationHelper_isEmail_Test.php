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

}
