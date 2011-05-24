<?php

use Orm\ValidationHelper;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\ValidationHelper::createDateTime
 */
class ValidationHelper_createDateTime_Test extends TestCase
{

	public function test()
	{
		$this->assertInstanceOf('DateTime', ValidationHelper::createDateTime('now'));
		$this->assertSame('2011-11-11T00:00:00+01:00', ValidationHelper::createDateTime('2011-11-11')->format('c'));
	}

}
