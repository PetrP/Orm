<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DibiCollection::getTotalCount
 */
class DibiCollection_getTotalCount_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->setExpectedException('Nette\NotImplementedException');
		$this->c->getTotalCount();
	}

}
