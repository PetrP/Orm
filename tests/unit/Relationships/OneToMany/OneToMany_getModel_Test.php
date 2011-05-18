<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\OneToMany::getModel
 */
class OneToMany_getModel_Test extends OneToMany_Test
{

	public function test()
	{
		$this->assertSame($this->e->getModel(), $this->o2m->getModel());
	}

}
