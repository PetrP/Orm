<?php

use Orm\NotImplementedException;

/**
 * @covers Orm\NotImplementedException
 */
class NotImplementedException_Test extends TestCase
{

	public function test()
	{
		$e = new NotImplementedException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\NotImplementedException', $e);
	}

}
