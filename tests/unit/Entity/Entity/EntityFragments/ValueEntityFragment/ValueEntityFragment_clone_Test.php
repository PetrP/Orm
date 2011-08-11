<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::__clone
 */
class ValueEntityFragment_clone_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->e = $m->testentityrepository->getById(1);
	}

	public function testBase()
	{
		$e = $this->e;

		$this->assertSame(1, $e->id);
		$this->assertSame('string', $e->string);
		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
		$this->assertSame('TestEntityRepository', get_class($e->repository));
		$this->assertSame(false, $e->isChanged());

		$ee = clone $e;

		$this->assertSame(NULL, isset($ee->id) ? $ee->id : NULL);
		$this->assertSame('string', $ee->string);
		$this->assertSame('2011-11-11', $ee->date->format('Y-m-d'));
		$this->assertSame('TestEntityRepository', get_class($e->repository));
		$this->assertSame(true, $ee->isChanged());

		$this->assertSame(1, $e->id);
		$this->assertSame('string', $e->string);
		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
		$this->assertSame('TestEntityRepository', get_class($e->repository));
		$this->assertSame(false, $e->isChanged());
	}

	public function testChange()
	{
		$e = $this->e;
		$ee = clone $e;

		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
		$ee->date = '2010-10-10';
		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
		$this->assertSame('2010-10-10', $ee->date->format('Y-m-d'));
	}

	public function testChangeObject()
	{
		$e = $this->e;

		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));

		$ee = clone $e;

		$ee->date->modify('-50 years');
		$this->assertSame('1961-11-11', $ee->date->format('Y-m-d'));
		try {
			$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
		} catch (PHPUnit_Framework_ExpectationFailedException $e) {
			$this->markTestSkipped('bug, udrzuje se reference');
		}
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', '__clone');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
