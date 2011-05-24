<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\AnnotationMetaData::builtParamsDefault
 * @covers Orm\AnnotationMetaData::builtSelf
 */
class AnnotationMetaData_builtParamsDefault_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$this->p = MockAnnotationMetaData::mockConstruct('MetaData_Test_Entity');
	}

	public function test()
	{
		$this->assertEquals(array(123), $this->p->builtParamsDefault('123'));
		$this->assertEquals(array(123), $this->p->builtParamsDefault('"123"'));
		$this->assertEquals(array('abc'), $this->p->builtParamsDefault('abc'));
		$this->assertEquals(array('abc'), $this->p->builtParamsDefault('"abc"'));
		$this->assertEquals(array('abc'), $this->p->builtParamsDefault("'abc'"));
		$this->assertEquals(array('true'), $this->p->builtParamsDefault("'true'"));
		$this->assertEquals(array('false'), $this->p->builtParamsDefault('"false"'));
		$this->assertEquals(array(true), $this->p->builtParamsDefault("true"));
		$this->assertEquals(array(false), $this->p->builtParamsDefault("false"));
		$this->assertEquals(array('xxx'), $this->p->builtParamsDefault("MetaData_Test_Entity::XXX"));
		$this->assertEquals(array('yyy'), $this->p->builtParamsDefault("self::YYY"));
	}

	public function testUnexistsConstant()
	{
		$this->setExpectedException('Nette\InvalidArgumentException', "'MetaData_Test_Entity' '{default SameClass::UNEXISTS_CONSTANT}': Constant SameClass::UNEXISTS_CONSTANT not exists");
		$this->p->builtParamsDefault('SameClass::UNEXISTS_CONSTANT');
	}

}
