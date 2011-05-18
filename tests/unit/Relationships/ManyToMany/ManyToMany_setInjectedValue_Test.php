<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\ManyToMany::setInjectedValue
 */
class ManyToMany_setInjectedValue_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->m2m->setInjectedValue(array(11, 10));
		$this->t(11, 10);
	}

	public function testNull()
	{
		$this->m2m->setInjectedValue(array(11, 10));
		$this->m2m->setInjectedValue(NULL);
		$this->t(11, 10);
	}

}
