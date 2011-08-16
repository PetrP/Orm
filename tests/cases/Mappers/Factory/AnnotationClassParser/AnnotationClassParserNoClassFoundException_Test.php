<?php

use Orm\AnnotationClassParserNoClassFoundException;

/**
 * @covers Orm\AnnotationClassParserNoClassFoundException
 */
class AnnotationClassParserNoClassFoundException_Test extends TestCase
{

	public function test()
	{
		$e = new AnnotationClassParserNoClassFoundException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\AnnotationClassParserException', $e);
		$this->assertInstanceOf('Orm\AnnotationClassParserNoClassFoundException', $e);
	}

}
