<?php

use Orm\AnnotationMetaDataException;

/**
 * @covers Orm\AnnotationMetaDataException
 */
class AnnotationMetaDataException_Test extends TestCase
{

	public function test()
	{
		$e = new AnnotationMetaDataException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\MetaDataException', $e);
		$this->assertInstanceOf('Orm\AnnotationMetaDataException', $e);
	}

}
