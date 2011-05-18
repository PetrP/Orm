<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\OneToMany::count
 */
class OneToMany_count_Test extends OneToMany_Test
{

	public function test()
	{
		$this->assertSame(4, $this->o2m->count());
	}

}
