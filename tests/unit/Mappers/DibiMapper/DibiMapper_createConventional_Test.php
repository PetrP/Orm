<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\DibiMapper::createConventional
 */
class DibiMapper_createConventional_Test extends TestCase
{
	public function test()
	{
		$m = new DibiMapper_createConventional_DibiMapper(new TestsRepository(new RepositoryContainer));
		$this->assertInstanceOf('Orm\SqlConventional', $m->__createConventional());
	}
}
