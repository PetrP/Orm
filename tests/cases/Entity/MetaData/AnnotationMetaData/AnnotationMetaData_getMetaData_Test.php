<?php

use Orm\MetaData;
use Orm\AnnotationMetaData;

/**
 * @covers Orm\AnnotationMetaData::getMetaData
 */
class AnnotationMetaData_getMetaData_Test extends TestCase
{

	public function testClassName()
	{
		$m = AnnotationMetaData::getMetaData('AnnotationMetaData_MockEntity');
		$this->assertInstanceOf('Orm\MetaData', $m);
		$this->assertSame('AnnotationMetaData_MockEntity', $m->getEntityClass());
	}

	public function testObject()
	{
		$m = AnnotationMetaData::getMetaData(new AnnotationMetaData_MockEntity);
		$this->assertInstanceOf('Orm\MetaData', $m);
		$this->assertSame('AnnotationMetaData_MockEntity', $m->getEntityClass());
	}

	public function testMetaData()
	{
		$m1 = new MetaData('AnnotationMetaData_MockEntity');
		$m2 = AnnotationMetaData::getMetaData($m1);
		$this->assertInstanceOf('Orm\MetaData', $m2);
		$this->assertSame($m1, $m2);
		$this->assertSame('AnnotationMetaData_MockEntity', $m2->getEntityClass());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AnnotationMetaData', 'getMetaData');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
