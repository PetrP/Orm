<?php

/**
 * @covers Orm\DibiPersistenceHelper::getWitchParamsNot
 */
class DibiPersistenceHelper_getWitchParamsNot_Test extends DibiPersistenceHelper_Test
{

	public function test()
	{
		$this->h->whichParamsNot = array('aaa', 'bbb');
		$this->assertSame(array('aaa', 'bbb'), $this->h->getWitchParamsNot());
		$this->assertSame(array('aaa', 'bbb'), $this->h->witchParamsNot);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiPersistenceHelper', 'getWitchParamsNot');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
