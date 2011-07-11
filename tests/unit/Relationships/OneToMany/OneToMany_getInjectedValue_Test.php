<?php

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
