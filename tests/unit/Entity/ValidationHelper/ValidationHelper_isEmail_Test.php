<?php

use Orm\ValidationHelper;

require_once dirname(__FILE__) . '/../../../boot.php';

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
