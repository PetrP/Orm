<?php

use Orm\MetaData;

/**
 * @covers Orm\ValueEntityFragment::hasParam
 */
class ValueEntityFragment_hasParam_Test extends TestCase
{

	public function testNoMode()
	{
		$e = new ValueEntityFragment_hasParam_Entity;
		$this->assertTrue($e->hasParam('read'));
		$this->assertTrue($e->hasParam('both'));
		$this->assertFalse($e->hasParam('none'));
	}

	public function testRead()
	{
		$e = new ValueEntityFragment_hasParam_Entity;
		$this->assertTrue($e->hasParam('read', MetaData::READ));
		$this->assertTrue($e->hasParam('both', MetaData::READ));
		$this->assertFalse($e->hasParam('none', MetaData::READ));
	}

	public function testReadWrite()
	{
		$e = new ValueEntityFragment_hasParam_Entity;
		$this->assertFalse($e->hasParam('read', MetaData::READWRITE));
		$this->assertTrue($e->hasParam('both', MetaData::READWRITE));
		$this->assertFalse($e->hasParam('none', MetaData::READWRITE));
	}

	public function testWrite()
	{
		$e = new ValueEntityFragment_hasParam_Entity;
		$this->assertFalse($e->hasParam('read', MetaData::WRITE));
		$this->assertTrue($e->hasParam('both', MetaData::WRITE));
		$this->assertFalse($e->hasParam('none', MetaData::WRITE));
	}

	public function testBadMode()
	{
		$e = new ValueEntityFragment_hasParam_Entity;
		$this->setExpectedException('Orm\InvalidArgumentException', 'Orm\Entity::hasParam() $mode must be Orm\MetaData::READWRITE, READ or WRITE; \'xyz\' given.');
		$e->hasParam('read', 'xyz');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', 'hasParam');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
