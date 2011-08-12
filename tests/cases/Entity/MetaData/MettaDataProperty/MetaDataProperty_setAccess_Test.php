<?php

use Orm\MetaData;
use Orm\MetaDataProperty;

/**
 * @covers Orm\MetaDataProperty::setAccess
 */
class MetaDataProperty_setAccess_Test extends TestCase
{
	private function setAccess($access, & $a = NULL)
	{
		$m = new MetaData('MetaData_Test_Entity');
		$property = new MetaDataProperty($m, 'id', 'null', $access);
		$a = $property->toArray();
		return (isset($a['get']) ? MetaData::READ : 0) | (isset($a['set']) ? MetaData::WRITE : 0);
	}

	public function testConstant()
	{
		$this->assertTrue((bool) (MetaData::READWRITE & MetaData::READ));
		$this->assertTrue((bool) (MetaData::READWRITE & MetaData::WRITE));
		$this->assertTrue((bool) (MetaData::READ & MetaData::READ));
		$this->assertTrue((bool) (MetaData::WRITE & MetaData::WRITE));
		$this->assertFalse((bool) (MetaData::READ & MetaData::WRITE));
		$this->assertFalse((bool) (MetaData::WRITE & MetaData::READ));
	}

	public function testNull()
	{
		$this->assertSame(MetaData::READWRITE, $this->setAccess(NULL));
	}

	public function testWrite()
	{
		$this->setExpectedException('Orm\MetaDataException', "Neni mozne vytvaret write-only polozky: MetaData_Test_Entity::\$id");
		$this->setAccess(MetaData::WRITE);
	}

	public function testRead()
	{
		$this->assertSame(MetaData::READ, $this->setAccess(MetaData::READ, $a));

		$this->assertSame(array('method' => 'getId'), $a['get']);
		$this->assertSame(NULL, $a['set']);
	}

	public function testReadWrite()
	{
		$this->assertSame(MetaData::READWRITE, $this->setAccess(MetaData::READWRITE, $a));

		$this->assertSame(array('method' => 'getId'), $a['get']);
		$this->assertSame(array('method' => NULL), $a['set']);
	}

	public function testNonSense()
	{
		$this->setExpectedException('Orm\MetaDataException', 'Orm\MetaDataProperty access is Orm\MetaData::READ or Orm\MetaData::READWRITE allowed');
		$this->setAccess('kukuk');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaDataProperty', 'setAccess');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
