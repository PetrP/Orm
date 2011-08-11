<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiMapper::getTableName
 */
class DibiMapper_getTableName_Test extends TestCase
{
	private $r;
	private $m;
	protected function setUp()
	{
		$this->r = new DibiMapper_getTableName_Repository(new RepositoryContainer);
		$this->m = new DibiMapper_getTableName_DibiMapper($this->r);
	}

	public function test()
	{
		$this->assertSame('dibimapper_gettablename_', $this->m->__getTableName());
	}

	public function testNamespace()
	{
		$this->r->__setRepositoryName('bla\bla');
		$this->assertSame('bla_bla', $this->m->__getTableName());
	}

	public function testNamespace2()
	{
		$this->r->__setRepositoryName('bla\bla\aasdasdasdsad654___\asdasd');
		$this->assertSame('bla_bla_aasdasdasdsad654____asdasd', $this->m->__getTableName());
	}

	public function testCache()
	{
		$this->assertAttributeSame(NULL, 'tableName', $this->m);
		$this->assertSame('dibimapper_gettablename_', $this->m->__getTableName());
		$this->assertAttributeSame('dibimapper_gettablename_', 'tableName', $this->m);
		$this->assertSame('dibimapper_gettablename_', $this->m->__getTableName());
	}

	public function testCache2()
	{
		$this->assertSame('dibimapper_gettablename_', $this->m->__getTableName());
		$this->r->__setRepositoryName('bla\bla\aasdasdasdsad654___\asdasd');
		$this->assertSame('dibimapper_gettablename_', $this->m->__getTableName());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'getTableName');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
