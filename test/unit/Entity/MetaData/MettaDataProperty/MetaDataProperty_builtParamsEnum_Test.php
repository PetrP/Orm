<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers MetaDataProperty::builtParamsEnum
 */
class MetaDataProperty_builtParamsEnum_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$this->p = new MetaDataProperty($m, 'id', 'null');
	}

	public function testInt()
	{
		$this->assertEquals(array(array(1,2,3), '1, 2, 3'), $this->p->builtParamsEnum('1,2,3'));
		$this->assertEquals(array(array(1,2,3), '"1", "2", "3"'), $this->p->builtParamsEnum('"1" , "2" , "3"'));
	}

	public function testFloat()
	{
		$this->assertEquals(array(array(1.5,2,3.9599959595), '1.5, 2, 3.9599959595'), $this->p->builtParamsEnum('1.5,2,3.9599959595'));
		$this->assertEquals(array(array(1.5), '"1.5"'), $this->p->builtParamsEnum('"1.5"'));
	}

	public function testString()
	{
		$this->assertEquals(array(array('abc', 'abc', 'abc'), '"abc", abc, \'abc\''), $this->p->builtParamsEnum('"abc" , abc , \'abc\''));
		$this->assertEquals(array(array('true'), '"true"'), $this->p->builtParamsEnum('"true"'));
	}

	public function testConstant()
	{
		$this->assertEquals(array(array(true, false, true), 'true, false, trUE'), $this->p->builtParamsEnum('true, false, trUE'));
	}

	public function testSelfConstant()
	{
		$this->assertEquals(array(array('xxx', 'yyy'), 'MetaData_Test_Entity::XXX, MetaData_Test_Entity::YYY'), $this->p->builtParamsEnum('MetaData_Test_Entity::XXX, MetaData_Test_Entity::YYY'));
		$this->assertEquals(array(array('xxx', 'yyy'), 'MetaData_Test_Entity::XXX, MetaData_Test_Entity::YYY'), $this->p->builtParamsEnum('self::XXX, self::YYY'));
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
		$this->assertEquals(array(array('a', 'c'), 'a, c'), $this->p->builtParamsEnum('MetaDataProperty_builtParamsEnum_Test::callback()'));
	}

	public function testSelfCallback()
	{
		$this->assertEquals(array(array('foo'), 'foo'), $this->p->builtParamsEnum('self::enum()'));
	}

	public static function invalidCallback()
	{
		return 'hůůů';
	}

	public function testInvalidCallback()
	{
		try {
			$this->p->builtParamsEnum('MetaDataProperty_builtParamsEnum_Test::xyz()');
		} catch (Exception $e) {}
		$this->assertException($e, 'InvalidStateException', "Callback 'MetaDataProperty_builtParamsEnum_Test::xyz' is not callable.");

		try {
			$this->p->builtParamsEnum('MetaDataProperty_builtParamsEnum_Test::invalidCallback()');
		} catch (Exception $e) {}
		$this->assertException($e, 'InvalidStateException', "'MetaData_Test_Entity' '{enum MetaDataProperty_builtParamsEnum_Test::invalidCallback()}': callback must return array, string given");
	}


}
