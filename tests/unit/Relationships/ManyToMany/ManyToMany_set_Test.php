<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers ManyToMany::set
 */
class ManyToMany_set_Test extends ManyToMany_Test
{

	public function test()
	{
		$e = new OneToMany_Entity;
		$this->m2m->set(array($e, 11));
		$this->t($e, 11);
	}

	public function testNull()
	{
		$this->m2m->set(array(NULL));
		$this->t();
	}

}
