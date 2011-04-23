<?php

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers Repository::getModel
 */
class Repository_getModel_Test extends TestCase
{

	public function test()
	{
		$m = new Model;
		$r = new TestsRepository($m);
		$this->assertSame($m, $r->getModel());
	}

}
