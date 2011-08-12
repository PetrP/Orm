<?php

/**
 * @covers Orm\DibiCollection::getConnection
 */
class DibiCollection_getConnection_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('DibiConnection', DibiCollection_DibiCollection::call($this->c, 'getConnection'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'getConnection');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
