<?php

use Orm\DibiPersistenceHelper;

/**
 * @covers Orm\DibiPersistenceHelper::getWitchParamsNot
 */
class DibiPersistenceHelper_getWitchParamsNot_Test extends TestCase
{

	public function test()
	{
		$h = new DibiPersistenceHelper;
		$h->whichParamsNot = array('aaa', 'bbb');
		$this->assertSame(array('aaa', 'bbb'), $h->getWitchParamsNot());
		$this->assertSame(array('aaa', 'bbb'), $h->witchParamsNot);
	}

}
