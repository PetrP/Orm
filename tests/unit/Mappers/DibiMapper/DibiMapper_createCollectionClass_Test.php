<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\DibiMapper::createCollectionClass
 */
class DibiMapper_createCollectionClass_Test extends TestCase
{
	public function test()
	{
		$m = new DibiMapper_createCollectionClass_DibiMapper(new TestsRepository(new RepositoryContainer));
		$this->assertSame('Orm\DibiCollection', $m->__createCollectionClass());
	}
}
