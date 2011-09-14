<?php

use Orm\MemberAccessException;

/**
 * @covers Orm\MemberAccessException
 */
class MemberAccessException_Test extends TestCase
{

	public function test()
	{
		$e = new MemberAccessException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\MemberAccessException', $e);
	}

}
