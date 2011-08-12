<?php

/**
 * @covers Orm\DibiCollection::getRepository
 */
class DibiCollection_getRepository_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('Orm\IRepository', DibiCollection_DibiCollection::call($this->c, 'getRepository'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'getRepository');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
