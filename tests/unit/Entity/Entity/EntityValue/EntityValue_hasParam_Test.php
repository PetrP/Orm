<?php

use Orm\MetaData;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\_EntityValue::hasParam
 */
class EntityValue_hasParam_Test extends TestCase
{

	public function testNoMode()
	{
		$e = new EntityValue_hasParam_Entity;
		$this->assertTrue($e->hasParam('read'));
		$this->assertTrue($e->hasParam('both'));
		$this->assertFalse($e->hasParam('none'));
	}

	public function testRead()
	{
		$e = new EntityValue_hasParam_Entity;
		$this->assertTrue($e->hasParam('read', MetaData::READ));
		$this->assertTrue($e->hasParam('both', MetaData::READ));
		$this->assertFalse($e->hasParam('none', MetaData::READ));
	}

	public function testReadWrite()
	{
		$e = new EntityValue_hasParam_Entity;
		$this->assertFalse($e->hasParam('read', MetaData::READWRITE));
		$this->assertTrue($e->hasParam('both', MetaData::READWRITE));
		$this->assertFalse($e->hasParam('none', MetaData::READWRITE));
	}

	public function testWrite()
	{
		$e = new EntityValue_hasParam_Entity;
		$this->assertFalse($e->hasParam('read', MetaData::WRITE));
		$this->assertTrue($e->hasParam('both', MetaData::WRITE));
		$this->assertFalse($e->hasParam('none', MetaData::WRITE));
	}

	public function testBadMode()
	{
		$e = new EntityValue_hasParam_Entity;
		$this->setExpectedException('InvalidArgumentException', 'Unknown mode');
		$e->hasParam('read', 'xyz');
	}

}
