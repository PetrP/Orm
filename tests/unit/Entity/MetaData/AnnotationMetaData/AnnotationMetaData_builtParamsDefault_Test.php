<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\AnnotationMetaData::builtParamsDefault
 * @covers Orm\AnnotationMetaData::builtSelf
 * @covers Orm\AnnotationMetaData::parseString
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
		$this->assertSame(array(123), $this->p->builtParamsDefault('123'));
		$this->assertSame(array('123'), $this->p->builtParamsDefault('"123"'));
		$this->assertSame(array('abc'), $this->p->builtParamsDefault('abc'));
		$this->assertSame(array('abc'), $this->p->builtParamsDefault('"abc"'));
		$this->assertSame(array('abc'), $this->p->builtParamsDefault("'abc'"));
		$this->assertSame(array('true'), $this->p->builtParamsDefault("'true'"));
		$this->assertSame(array('false'), $this->p->builtParamsDefault('"false"'));
		$this->assertSame(array(true), $this->p->builtParamsDefault("true"));
		$this->assertSame(array(false), $this->p->builtParamsDefault("false"));
		$this->assertSame(array('xxx'), $this->p->builtParamsDefault("MetaData_Test_Entity::XXX"));
		$this->assertSame(array('yyy'), $this->p->builtParamsDefault("self::YYY"));
		$this->assertSame(array(NULL), $this->p->builtParamsDefault('NULL'));
		$this->assertSame(array(''), $this->p->builtParamsDefault('""'));
		$this->assertSame(array(''), $this->p->builtParamsDefault("''"));
		$this->assertSame(array(''), $this->p->builtParamsDefault(""));
		$this->assertSame(array(0), $this->p->builtParamsDefault("0"));
		$this->assertSame(array(5.32), $this->p->builtParamsDefault("5.32"));
	}

	public function testUnexistsConstant()
	{
		$this->setExpectedException('Nette\InvalidArgumentException', "'MetaData_Test_Entity' '{default SameClass::UNEXISTS_CONSTANT}': Constant SameClass::UNEXISTS_CONSTANT not exists");
		$this->p->builtParamsDefault('SameClass::UNEXISTS_CONSTANT');
	}

}
