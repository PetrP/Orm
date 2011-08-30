<?php

use Orm\DibiPersistenceHelper;

/**
 * @covers Orm\DibiPersistenceHelper::setWitchParamsNot
 */
class DibiPersistenceHelper_setWitchParamsNot_Test extends TestCase
{

	public function test()
	{
		$h = new DibiPersistenceHelper;
		$h->setWitchParamsNot(array('bbb', 'aaa'));
		$this->assertSame(array('bbb', 'aaa'), $h->whichParamsNot);
		$h->witchParamsNot = array('aaa', 'bbb');
		$this->assertSame(array('aaa', 'bbb'), $h->whichParamsNot);
	}

}
