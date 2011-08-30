<?php

use Orm\DibiPersistenceHelper;

/**
 * @covers Orm\DibiPersistenceHelper::getWitchParams
 */
class DibiPersistenceHelper_getWitchParams_Test extends TestCase
{

	public function test()
	{
		$h = new DibiPersistenceHelper;
		$h->whichParams = array('aaa', 'bbb');
		$this->assertSame(array('aaa', 'bbb'), $h->getWitchParams());
		$this->assertSame(array('aaa', 'bbb'), $h->witchParams);
	}

}
