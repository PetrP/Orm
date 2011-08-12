<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::hydrateEntity
 */
class Repository_hydrateEntity_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->tests;
	}

	public function test()
	{
		$e = $this->r->hydrateEntity(array('id' => 1,'string' => 'xyz'));
		$ee = $this->r->hydrateEntity(array('id' => 1,));
		$this->assertInstanceOf('TestEntity', $e);
		$this->assertSame($ee, $e);
		$this->assertSame('xyz', $ee->string);
	}

	public function testNoId()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Data, that is returned from storage, doesn't contain id.");
		$this->r->hydrateEntity(array());
	}

	public function testEmptyId()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Data, that is returned from storage, doesn't contain id.");
		$this->r->hydrateEntity(array('id' => ''));
	}

	public function testPrimaryKey()
	{
		$c = new NoConventional_getPrimaryKey_NoConventional;
		setAccessible(new ReflectionProperty('Orm\Repository', 'conventional'))
			->setValue($this->r, $c)
		;
		setAccessible(new ReflectionProperty('Orm\Repository', 'primaryKey'))
			->setValue($this->r, $c->getPrimaryKey())
		;
		$e = $this->r->hydrateEntity(array('foo_bar' => 1));
		$this->assertSame(1, $e->id);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'hydrateEntity');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
