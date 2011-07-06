<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

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
		$this->assertEquals(array('MetaData_Test_Entity::method'), $this->p->builtParamsInjection("MetaData_Test_Entity::method"));
		$this->assertEquals(array('MetaData_Test_Entity::method'), $this->p->builtParamsInjection("self::method"));
		$this->assertEquals(array('MetaData_Test_Entity::method'), $this->p->builtParamsInjection("MetaData_Test_Entity::method()"));
		$this->assertEquals(array('MetaData_Test_Entity::method'), $this->p->builtParamsInjection("self::method()"));
	}

}
