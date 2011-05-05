<?php

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers ManyToMany::count
 */
class ManyToMany_count_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->assertSame(4, $this->m2m->count());
	}

}
