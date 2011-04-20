<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers MetaDataProperty::builtParamsDefault
 */
class MetaDataProperty_builtParamsDefault_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$this->p = new MetaDataProperty($m, 'id', 'null');
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

}
