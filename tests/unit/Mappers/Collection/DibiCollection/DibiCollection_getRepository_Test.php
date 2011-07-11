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

}
