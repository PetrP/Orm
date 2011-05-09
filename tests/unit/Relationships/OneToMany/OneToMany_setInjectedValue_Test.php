<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers OneToMany::setInjectedValue
 */
class OneToMany_setInjectedValue_Test extends OneToMany_Test
{

	public function test()
	{
		$this->o2m->setInjectedValue(array(11, 10));
		$this->t(11, 10);
	}

	public function testNull()
	{
		$this->o2m->setInjectedValue(array(11, 10));
		$this->o2m->setInjectedValue(NULL);
		$this->t(11, 10);
	}

}
