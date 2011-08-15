<?php

use Orm\MetaData;
use Orm\AnnotationMetaData;

/**
 * @covers Orm\AnnotationMetaData::__construct
 * @see AnnotationMetaData_Test
 */
class AnnotationMetaData_construct_Test extends TestCase
{

	public function test()
	{
		$m = MockAnnotationMetaData::mockConstruct('AnnotationMetaData_MockEntity');
		$this->assertInstanceOf('Orm\AnnotationMetaData', $m);
		$this->assertAttributeSame('AnnotationMetaData_MockEntity', 'class', $m);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AnnotationMetaData', '__construct');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
