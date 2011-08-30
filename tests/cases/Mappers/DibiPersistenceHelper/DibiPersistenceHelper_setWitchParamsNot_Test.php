<?php

/**
 * @covers Orm\DibiPersistenceHelper::setWitchParamsNot
 */
class DibiPersistenceHelper_setWitchParamsNot_Test extends DibiPersistenceHelper_Test
{

	public function test()
	{
		$this->h->setWitchParamsNot(array('bbb', 'aaa'));
		$this->assertSame(array('bbb', 'aaa'), $this->h->whichParamsNot);
		$this->h->witchParamsNot = array('aaa', 'bbb');
		$this->assertSame(array('aaa', 'bbb'), $this->h->whichParamsNot);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiPersistenceHelper', 'setWitchParamsNot');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
