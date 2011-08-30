<?php

/**
 * @covers Orm\DibiPersistenceHelper::getWitchParams
 */
class DibiPersistenceHelper_getWitchParams_Test extends DibiPersistenceHelper_Test
{

	public function test()
	{
		$this->h->whichParams = array('aaa', 'bbb');
		$this->assertSame(array('aaa', 'bbb'), $this->h->getWitchParams());
		$this->assertSame(array('aaa', 'bbb'), $this->h->witchParams);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiPersistenceHelper', 'getWitchParams');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
