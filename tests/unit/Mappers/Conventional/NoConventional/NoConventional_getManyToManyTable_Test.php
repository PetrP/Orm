<?php

use Orm\RepositoryContainer;
use Orm\NoConventional;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\NoConventional::getManyToManyTable
 */
class NoConventional_getManyToManyTable_Test extends TestCase
{
	private $c;
	private $r1;
	private $r2;
	protected function setUp()
	{
		$this->c = new NoConventional;
		$this->r1 = new SqlConventional_TestRepository(new RepositoryContainer);
		$this->r2 = new DibiMapper_getTableName_Repository(new RepositoryContainer);
	}

	public function test()
	{
		$this->assertSame('sqlconventional_test_x_dibimapper_gettablename_', $this->c->getManyToManyTable($this->r1, $this->r2));
		$this->assertSame('dibimapper_gettablename__x_sqlconventional_test', $this->c->getManyToManyTable($this->r2, $this->r1));
	}

	public function testNamespace()
	{
		$this->r2->__setRepositoryName('namespace\tests');
		$this->assertSame('sqlconventional_test_x_namespace_tests', $this->c->getManyToManyTable($this->r1, $this->r2));
		$this->assertSame('namespace_tests_x_sqlconventional_test', $this->c->getManyToManyTable($this->r2, $this->r1));
		$this->assertSame('namespace_tests_x_namespace_tests', $this->c->getManyToManyTable($this->r2, $this->r2));
	}

}
