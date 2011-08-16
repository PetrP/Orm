<?php

use Orm\AnnotationClassParserException;

/**
 * @covers Orm\AnnotationClassParserException
 */
class AnnotationClassParserException_Test extends TestCase
{

	public function test()
	{
		$e = new AnnotationClassParserException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\AnnotationClassParserException', $e);
	}

}
