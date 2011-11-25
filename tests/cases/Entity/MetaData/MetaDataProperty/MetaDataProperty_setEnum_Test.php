<?php

use Orm\MetaData;
use Orm\MetaDataProperty;

/**
 * @covers Orm\MetaDataProperty::setEnum
 */
class MetaDataProperty_setEnum_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$this->p = new MetaDataProperty($m, 'id', 'null');
	}

	private function getEnum()
	{
		$a = $this->p->toArray();
		return $a['enum'];
	}

	public function test()
	{
		$this->p->setEnum(array(1,2,3));
		$this->assertSame(array('constants' => array(1,2,3), 'original' => '1, 2, 3'), $this->getEnum());

		$this->p->setEnum(array('xxx'), 'MetaData_Test_Entity::XXX');
		$this->assertSame(array('constants' => array('xxx'), 'original' => 'MetaData_Test_Entity::XXX'), $this->getEnum());
	}

	public function testWithNull()
	{
		$this->p->setEnum(array(NULL, '', 0, '0', false));
		$this->assertSame(array('constants' => array(NULL, '', 0, '0', false), 'original' => "NULL, '', 0, '0', FALSE"), $this->getEnum());
	}

	public function testDetectOriginal()
	{
		$this->p->setEnum(array(NULL, '', 0, false, true, 'string', 1.2, $this, array(1,2)));
		$this->assertSame(array(
			'constants' => array(NULL, '', 0, false, true, 'string', 1.2, $this, array(1,2)),
			'original' => "NULL, '', 0, FALSE, TRUE, 'string', 1.2, MetaDataProperty_setEnum_Test, array",
		), $this->getEnum());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaDataProperty', 'setEnum');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
