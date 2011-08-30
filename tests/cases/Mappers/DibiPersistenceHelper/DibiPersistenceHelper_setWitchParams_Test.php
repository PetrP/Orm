<?php

/**
 * @covers Orm\DibiPersistenceHelper::setWitchParams
 */
class DibiPersistenceHelper_setWitchParams_Test extends DibiPersistenceHelper_Test
{

	public function test()
	{
		$this->h->setWitchParams(array('bbb', 'aaa'));
		$this->assertSame(array('bbb', 'aaa'), $this->h->whichParams);
		$this->h->witchParams = array('aaa', 'bbb');
		$this->assertSame(array('aaa', 'bbb'), $this->h->whichParams);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiPersistenceHelper', 'setWitchParams');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
