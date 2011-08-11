<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiMapper::getPrimaryKey
 */
class DibiMapper_getPrimaryKey_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new DibiMapper_getPrimaryKey_DibiMapper(new TestsRepository(new RepositoryContainer));
	}

	public function test1()
	{
		$this->assertAttributeSame(NULL, 'primaryKey', $this->m);
		$this->assertSame('id', $this->m->__getPrimaryKey());
		$this->assertAttributeSame('id', 'primaryKey', $this->m);
		$this->assertSame('id', $this->m->__getPrimaryKey());
	}

	public function test2()
	{
		$this->m->c = new SqlConventional_getPrimaryKey_SqlConventional($this->m);
		$this->assertAttributeSame(NULL, 'primaryKey', $this->m);
		$this->assertSame('foo_bar', $this->m->__getPrimaryKey());
		$this->assertAttributeSame('foo_bar', 'primaryKey', $this->m);
		$this->assertSame('foo_bar', $this->m->__getPrimaryKey());
	}

	public function testFinal()
	{
		$r = new ReflectionMethod($this->m, 'getPrimaryKey');
		$this->assertTrue($r->isFinal());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'getPrimaryKey');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
