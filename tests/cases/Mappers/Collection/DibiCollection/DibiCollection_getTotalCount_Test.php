<?php

/**
 * @covers Orm\DibiCollection::getTotalCount
 */
class DibiCollection_getTotalCount_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->setExpectedException('Orm\NotImplementedException');
		$this->c->getTotalCount();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiCollection', 'getTotalCount');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
