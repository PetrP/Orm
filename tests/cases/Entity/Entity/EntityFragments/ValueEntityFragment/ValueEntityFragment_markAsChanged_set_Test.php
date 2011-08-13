<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::markAsChanged
 */
class ValueEntityFragment_markAsChanged_set_Test extends TestCase
{
	private $r;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->TestEntityRepository;
	}

	public function testSet1()
	{
		$e = new TestEntity;
		$this->assertSame(true, $e->isChanged());
		$this->assertSame(true, $e->isChanged('string'));
		$this->assertSame(true, $e->isChanged('date'));
		$e->markAsChanged();
		$this->assertSame(true, $e->isChanged());
		$this->assertSame(true, $e->isChanged('string'));
		$this->assertSame(true, $e->isChanged('date'));
	}

	public function testSet2()
	{
		$e = $this->r->getById(1);
		$this->assertSame(false, $e->isChanged());
		$this->assertSame(false, $e->isChanged('string'));
		$this->assertSame(false, $e->isChanged('date'));
		$e->markAsChanged();
		$this->assertSame(true, $e->isChanged());
		$this->assertSame(true, $e->isChanged('string'));
		$this->assertSame(true, $e->isChanged('date'));
	}

	public function testJustOne()
	{
		$e = $this->r->getById(1);
		$this->assertSame(false, $e->isChanged());
		$this->assertSame(false, $e->isChanged('string'));
		$this->assertSame(false, $e->isChanged('date'));
		$e->markAsChanged('string');
		$this->assertSame(true, $e->isChanged());
		$this->assertSame(true, $e->isChanged('string'));
		$this->assertSame(false, $e->isChanged('date'));
	}

	public function testReturns()
	{
		$e1 = new TestEntity;
		$e2 = $this->r->getById(1);
		$this->assertSame($e1, $e1->markAsChanged());
		$this->assertSame($e1, $e1->markAsChanged('string'));
		$this->assertSame($e2, $e2->markAsChanged());
		$this->assertSame($e2, $e2->markAsChanged('string'));
	}

	public function testUnknown()
	{
		$e = $this->r->getById(1);
		$this->setExpectedException('Orm\PropertyAccessException', 'Cannot mark as changed an undeclared property TestEntity::$unknown.');
		$e->markAsChanged('unknown');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', 'markAsChanged');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
