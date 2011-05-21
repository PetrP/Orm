<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DibiCollection::findBy
 */
class DibiCollection_findBy_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$c = $this->c->findBy(array('x' => 'y'));
		$this->assertInstanceOf('Orm\DibiCollection', $c);
		$this->assertNotSame($this->c, $c);
		$this->assertAttributeSame(array(), 'findBy', $this->c);
		$this->assertAttributeSame(array(array('x' => 'y')), 'findBy', $c);

	}

}
