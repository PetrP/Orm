<?php

use Orm\MetaDataException;

/**
 * @covers Orm\MetaDataException
 */
class MetaDataException_Test extends TestCase
{

	public function test()
	{
		$e = new MetaDataException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\MetaDataException', $e);
	}

}
