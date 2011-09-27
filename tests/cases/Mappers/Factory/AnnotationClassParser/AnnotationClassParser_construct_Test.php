<?php

use Orm\AnnotationClassParser;
use Orm\AnnotationsParser;

/**
 * @covers Orm\AnnotationClassParser::__construct
 */
class AnnotationClassParser_construct_Test extends TestCase
{

	public function testParserSet()
	{
		$pp = new AnnotationsParser;
		$p = new AnnotationClassParser($pp);
		$this->assertAttributeInstanceOf('Orm\AnnotationsParser', 'parser', $p);
		$this->assertAttributeSame($pp, 'parser', $p);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AnnotationClassParser', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
