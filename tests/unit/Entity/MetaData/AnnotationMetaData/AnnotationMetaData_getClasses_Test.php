<?php

use Orm\AnnotationMetaData;

/**
 * @covers Orm\AnnotationMetaData::getClasses
 * @see AnnotationMetaData_Test
 */
class AnnotationMetaData_getClasses_Test extends TestCase
{
	public function testNotImplement()
	{
		$x = AnnotationMetaData::getMetaData('AnnotationMetaData_getClasses_Entity_A1')->toArray();
		$this->assertSame(array('a2'), array_keys($x));
		$x = AnnotationMetaData::getMetaData('AnnotationMetaData_getClasses_Entity_A2')->toArray();
		$this->assertSame(array('a2', 'a3'), array_keys($x));
	}

	public function testNotParent()
	{
		$x = AnnotationMetaData::getMetaData('AnnotationMetaData_getClasses_Entity_B1')->toArray();
		$this->assertSame(array('b1', 'b2'), array_keys($x));
	}
}
