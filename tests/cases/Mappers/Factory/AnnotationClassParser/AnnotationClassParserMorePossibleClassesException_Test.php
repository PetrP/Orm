<?php

use Orm\AnnotationClassParserMorePossibleClassesException;

/**
 * @covers Orm\AnnotationClassParserMorePossibleClassesException
 */
class AnnotationClassParserMorePossibleClassesException_Test extends TestCase
{

	public function test()
	{
		$e = new AnnotationClassParserMorePossibleClassesException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\AnnotationClassParserException', $e);
		$this->assertInstanceOf('Orm\AnnotationClassParserMorePossibleClassesException', $e);
	}

}
