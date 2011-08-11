<?php

use Orm\RepositoryContainer;
use Orm\NoConventional;

/**
 * @covers Orm\NoConventional::getTable
 */
class NoConventional_getTable_Test extends TestCase
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
		$this->assertSame('sqlconventional_test', $this->c->getTable($this->r1));
		$this->assertSame('dibimapper_gettablename_', $this->c->getTable($this->r2));
	}

	public function testNamespace()
	{
		$this->r2->__setRepositoryName('namespace\tests');
		$this->assertSame('namespace_tests', $this->c->getTable($this->r2));
		$this->r2->__setRepositoryName('namespace\namespace\foo');
		$this->assertSame('namespace_namespace_foo', $this->c->getTable($this->r2));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\NoConventional', 'getTable');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
