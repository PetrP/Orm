<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Mapper::getModel
 */
class Mapper_getModel_Test extends TestCase
{

	public function test()
	{
		$model = new RepositoryContainer;
		$m = new TestsMapper(new TestsRepository($model));
		$this->assertSame($model, $m->getModel());
	}

}
