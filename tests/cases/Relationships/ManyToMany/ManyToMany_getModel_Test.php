<?php

/**
 * @covers Orm\ManyToMany::getModel
 */
class ManyToMany_getModel_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->assertSame($this->e->getModel(), $this->m2m->getModel());
	}

}
