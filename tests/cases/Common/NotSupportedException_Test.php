<?php

use Orm\NotSupportedException;

/**
 * @covers Orm\NotSupportedException
 */
class NotSupportedException_Test extends TestCase
{

	public function test()
	{
		$e = new NotSupportedException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\NotSupportedException', $e);
	}

}
