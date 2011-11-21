<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::hydrateEntity
 * @covers Orm\IdentityMap::create
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
		$this->setExpectedException('Orm\BadReturnException', "Data, that is returned from storage, doesn't contain id.");
		$this->r->hydrateEntity(array());
	}

	public function testEmptyId()
	{
		$this->setExpectedException('Orm\NotValidException', "Param TestEntity::\$id must be 'id'; '' given.");
		$this->r->hydrateEntity(array('id' => ''));
	}

	public function testEmptyIdZero()
	{
		$this->setExpectedException('Orm\NotValidException', "Param TestEntity::\$id must be 'id'; '0' given.");
		$this->r->hydrateEntity(array('id' => '0'));
	}

	public function testEmptyIdNull()
	{
		$this->setExpectedException('Orm\BadReturnException', "Data, that is returned from storage, doesn't contain id.");
		$this->r->hydrateEntity(array('id' => NULL));
	}

	public function testPrimaryKey()
	{
		$c = new NoConventional_getPrimaryKey_NoConventional;
		$im = $this->readAttribute($this->r, 'identityMap');
		setAccessible(new ReflectionProperty('Orm\IdentityMap', 'conventional'))
			->setValue($im, $c)
		;
		setAccessible(new ReflectionProperty('Orm\IdentityMap', 'primaryKey'))
			->setValue($im, $c->getPrimaryKey())
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
