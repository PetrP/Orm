<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\ArrayMapper::createCollectionClass
 */
class ArrayMapper_createCollectionClass_Test extends TestCase
{
	public function test()
	{
		$m = new ArrayMapper_createCollectionClass_ArrayMapper(new TestsRepository(new RepositoryContainer));
		$this->assertSame('Orm\ArrayCollection', $m->__createCollectionClass());
	}
}
