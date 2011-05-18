<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

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
