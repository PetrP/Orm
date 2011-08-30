<?php

use Orm\DibiPersistenceHelper;

/**
 * @covers Orm\DibiPersistenceHelper::setWitchParams
 */
class DibiPersistenceHelper_setWitchParams_Test extends TestCase
{

	public function test()
	{
		$h = new DibiPersistenceHelper;
		$h->setWitchParams(array('bbb', 'aaa'));
		$this->assertSame(array('bbb', 'aaa'), $h->whichParams);
		$h->witchParams = array('aaa', 'bbb');
		$this->assertSame(array('aaa', 'bbb'), $h->whichParams);
	}

}
