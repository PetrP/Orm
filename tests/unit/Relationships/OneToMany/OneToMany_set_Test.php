<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\OneToMany::set
 */
class OneToMany_set_Test extends OneToMany_Test
{

	public function test()
	{
		$e = new OneToMany_Entity;
		$this->o2m->set(array($e, 11));
		$this->t($e, 11);
	}

	public function testNull()
	{
		$this->o2m->set(array(NULL));
		$this->t();
	}

}
