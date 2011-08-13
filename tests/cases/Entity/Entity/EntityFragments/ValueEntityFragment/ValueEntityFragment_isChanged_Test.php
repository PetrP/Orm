<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::isChanged
 */
class ValueEntityFragment_isChanged_Test extends TestCase
{
	private $r;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->TestEntityRepository;
	}

	public function testCreate()
	{
		$e = new TestEntity;
		$this->assertSame(true, $e->isChanged());
		$this->assertSame(true, $e->isChanged('string'));
		$this->assertSame(true, $e->isChanged('date'));
	}

	public function testLoad()
	{
		$e = $this->r->getById(1);
		$this->assertSame(false, $e->isChanged());
		$this->assertSame(false, $e->isChanged('string'));
		$this->assertSame(false, $e->isChanged('date'));
	}

	public function testSet()
	{
		$e = $this->r->getById(1);
		$e->string = 'xyz';
		$this->assertSame(true, $e->isChanged());
		$this->assertSame(true, $e->isChanged('string'));
		$this->assertSame(false, $e->isChanged('date'));
	}

	public function testPersist()
	{
		$e = $this->r->getById(1);
		$e->string = 'xyz';
		$this->r->persist($e);
		$this->assertSame(false, $e->isChanged());
		$this->assertSame(false, $e->isChanged('string'));
		$this->assertSame(false, $e->isChanged('date'));
	}

	public function testGet()
	{
		$e = $this->r->getById(1);
		$e->string;
		$this->assertSame(false, $e->isChanged());
		$this->assertSame(false, $e->isChanged('string'));
		$this->assertSame(false, $e->isChanged('date'));
	}

	public function testSetAndGet()
	{
		$e = $this->r->getById(1);
		$e->date = 'now';
		$e->string;
		$this->assertSame(true, $e->isChanged());
		$this->assertSame(false, $e->isChanged('string'));
		$this->assertSame(true, $e->isChanged('date'));
	}

	public function testRemove()
	{
		$e = $this->r->getById(1);
		$this->r->remove($e);
		$this->assertSame(true, $e->isChanged());
		$this->assertSame(true, $e->isChanged('string'));
		$this->assertSame(true, $e->isChanged('date'));
	}

	public function testUnknown()
	{
		$e = $this->r->getById(1);
		$this->setExpectedException('Orm\PropertyAccessException', 'Cannot check an undeclared property TestEntity::$unknown.');
		$e->isChanged('unknown');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', 'isChanged');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
