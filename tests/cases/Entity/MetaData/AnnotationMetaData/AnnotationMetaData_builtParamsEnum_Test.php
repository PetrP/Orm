<?php

/**
 * @covers Orm\AnnotationMetaData::builtParamsEnum
 * @covers Orm\AnnotationMetaData::parseSelf
 * @covers Orm\AnnotationMetaData::parseString
 */
class AnnotationMetaData_builtParamsEnum_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$this->p = MockAnnotationMetaData::mockConstruct('MetaData_Test_Entity');
	}

	public function testInt()
	{
		$this->assertSame(array(array(1,2,3), '1, 2, 3'), $this->p->builtParamsEnum('1,2,3'));
		$this->assertSame(array(array('1','2','3'), '"1", "2", "3"'), $this->p->builtParamsEnum('"1" , "2" , "3"'));
	}

	public function testFloat()
	{
		$this->assertSame(array(array(1.5,2,3.9599959595), '1.5, 2, 3.9599959595'), $this->p->builtParamsEnum('1.5,2,3.9599959595'));
		$this->assertSame(array(array('1.5'), '"1.5"'), $this->p->builtParamsEnum('"1.5"'));
	}

	public function testString()
	{
		$this->assertSame(array(array('abc', 'abc', 'abc'), '"abc", abc, \'abc\''), $this->p->builtParamsEnum('"abc" , abc , \'abc\''));
		$this->assertSame(array(array('true'), '"true"'), $this->p->builtParamsEnum('"true"'));
	}

	public function testConstant()
	{
		$this->assertSame(array(array(true, false, true), 'true, false, trUE'), $this->p->builtParamsEnum('true, false, trUE'));
	}

	public function testSelfConstant()
	{
		$this->assertSame(array(array('xxx', 'yyy'), 'MetaData_Test_Entity::XXX, MetaData_Test_Entity::YYY'), $this->p->builtParamsEnum('MetaData_Test_Entity::XXX, MetaData_Test_Entity::YYY'));
		$this->assertSame(array(array('xxx', 'yyy'), 'MetaData_Test_Entity::XXX, MetaData_Test_Entity::YYY'), $this->p->builtParamsEnum('self::XXX, self::YYY'));
	}

	public static function callback()
	{
		return array(
			'a' => 'b',
			'c' => 'd',
		);
	}

	public function testCallback()
	{
		$this->assertSame(array(array('a', 'c'), 'a, c'), $this->p->builtParamsEnum('AnnotationMetaData_builtParamsEnum_Test::callback()'));
	}

	public function testSelfCallback()
	{
		$this->assertSame(array(array('foo'), 'foo'), $this->p->builtParamsEnum('self::enum()'));
	}

	public static function invalidCallback()
	{
		return 'hůůů';
	}

	public function testInvalidCallback1()
	{
		$this->setExpectedException('Orm\NotCallableException', "Callback 'AnnotationMetaData_builtParamsEnum_TestX::xyz' is not callable.");
		$this->p->builtParamsEnum('AnnotationMetaData_builtParamsEnum_TestX::xyz()');
	}

	public function testInvalidCallback2()
	{
		$this->setExpectedException('Orm\AnnotationMetaDataException', "'MetaData_Test_Entity' '{enum AnnotationMetaData_builtParamsEnum_Test::invalidCallback()}': callback must return array, string given");
		$this->p->builtParamsEnum('AnnotationMetaData_builtParamsEnum_Test::invalidCallback()');
	}

	public function testUnexistsConstant()
	{
		$this->setExpectedException('Orm\AnnotationMetaDataException', "'MetaData_Test_Entity' '{enum SameClass::UNEXISTS_CONSTANT}': Constant SameClass::UNEXISTS_CONSTANT not exists");
		$this->p->builtParamsEnum('SameClass::UNEXISTS_CONSTANT');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AnnotationMetaData', 'builtParamsEnum');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
