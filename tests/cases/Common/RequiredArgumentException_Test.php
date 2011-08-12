<?php

use Orm\RequiredArgumentException;

/**
 * @covers Orm\RequiredArgumentException
 */
class RequiredArgumentException_Test extends TestCase
{

	public function test()
	{
		$e = new RequiredArgumentException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\RequiredArgumentException', $e);
	}

}
