<?php

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
