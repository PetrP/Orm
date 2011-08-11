<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\SqlConventional::getManyToManyTable
 */
class SqlConventional_getManyToManyTable_Test extends TestCase
{
	private $c;
	private $r1;
	private $r2;
	protected function setUp()
	{
		$this->c = new MockSqlConventional;
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

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\NoConventional', 'getManyToManyTable');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
