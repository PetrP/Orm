<?php

use Orm\MetaData;
use Orm\MetaDataProperty;

/**
 * @covers Orm\MetaDataProperty::setDefault
 */
class MetaDataProperty_setDefault_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$this->p = new MetaDataProperty($m, 'id', 'null');
	}

	private function setDefault()
	{
		$a = $this->p->toArray();
		return $a['default'];
	}

	public function test()
	{
		$this->p->setDefault(MetaData_Test_Entity::XXX);
		$this->assertSame(MetaData_Test_Entity::XXX, $this->setDefault());
	}

	public function testEmpty()
	{
		$this->p->setDefault('');
		$this->assertSame('', $this->setDefault());
	}

	public function testNull()
	{
		$this->p->setDefault(NULL);
		$this->assertSame(NULL, $this->setDefault());
	}

	public function testBool()
	{
		$this->p->setDefault(false);
		$this->assertSame(false, $this->setDefault());
	}

	public function testInt()
	{
		$this->p->setDefault(123);
		$this->assertSame(123, $this->setDefault());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaDataProperty', 'setDefault');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
