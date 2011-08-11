<?php

/**
 * @covers Orm\AnnotationMetaData::builtParamsInjection
 * @covers Orm\AnnotationMetaData::parseSelf
 */
class AnnotationMetaData_builtParamsInjection_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$this->p = MockAnnotationMetaData::mockConstruct('MetaData_Test_Entity');
	}

	public function test()
	{
		$this->assertSame(array('MetaData_Test_Entity::method'), $this->p->builtParamsInjection("MetaData_Test_Entity::method"));
		$this->assertSame(array('MetaData_Test_Entity::method'), $this->p->builtParamsInjection("self::method"));
		$this->assertSame(array('MetaData_Test_Entity::method'), $this->p->builtParamsInjection("MetaData_Test_Entity::method()"));
		$this->assertSame(array('MetaData_Test_Entity::method'), $this->p->builtParamsInjection("self::method()"));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AnnotationMetaData', 'builtParamsInjection');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
