<?php

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers Mapper::getModel
 */
class Mapper_getModel_Test extends TestCase
{

	public function test()
	{
		$model = new Model;
		$m = new TestsMapper(new TestsRepository($model));
		$this->assertSame($model, $m->getModel());
	}

}
