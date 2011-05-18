<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\OneToMany::getInjectedValue
 */
class OneToMany_getInjectedValue_Test extends OneToMany_Test
{

	public function test()
	{
		$this->assertNull($this->o2m->getInjectedValue());
	}

}
