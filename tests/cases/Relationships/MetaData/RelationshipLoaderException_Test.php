<?php

use Orm\RelationshipLoaderException;

/**
 * @covers Orm\RelationshipLoaderException
 */
class RelationshipLoaderException_Test extends TestCase
{

	public function test()
	{
		$e = new RelationshipLoaderException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\MetaDataException', $e);
		$this->assertInstanceOf('Orm\RelationshipLoaderException', $e);
	}

}
