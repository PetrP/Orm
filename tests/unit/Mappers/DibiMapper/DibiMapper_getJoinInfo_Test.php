<?php

use Orm\RepositoryContainer;
use Orm\DibiMapper;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\DibiMapper::getJoinInfo
 * @see DibiCollection_join_ManyToMany_Test
 * @see DibiCollection_join_OneToMany_Test
 * @see DibiCollection_join_Test
 */
class DibiMapper_getJoinInfo_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new DibiMapper(new TestsRepository(new RepositoryContainer));
	}

	public function testNoArrow()
	{
		$this->assertNull($this->m->getJoinInfo('blaBla'));
	}

}
