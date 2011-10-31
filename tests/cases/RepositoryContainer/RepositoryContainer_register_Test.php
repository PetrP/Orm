<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\RepositoryContainer::register
 */
class RepositoryContainer_register_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
	}

	public function testBCCanByReregister1()
	{
		$this->m->register('tests', 'TestsRepository');
		$this->assertInstanceOf('TestsRepository', $this->m->tests);
		$this->assertSame($this->m->TestsRepository, $this->m->tests);
	}

	public function testBCCanByReregister2()
	{
		$this->m->register('tests', 'TestEntityRepository');
		$this->assertInstanceOf('TestEntityRepository', $this->m->tests);
		$this->assertSame($this->m->TestEntityRepository, $this->m->tests);
	}

	public function testBCCanByReregister3()
	{
		$this->assertInstanceOf('TestsRepository', $this->m->tests);
		$this->setExpectedException('Orm\RepositoryAlreadyRegisteredException', "Repository alias 'tests' is already registered");
		$this->m->register('tests', 'TestsRepository');
	}

	public function testOld2()
	{
		$this->setExpectedException('Orm\RepositoryAlreadyRegisteredException', "Repository alias 'testsrepository' is already registered");
		$this->m->register('TestsRepository', 'TestsRepository');
	}

	public function testAlias()
	{
		$this->m->register('xyz', 'TestsRepository');
		$this->assertInstanceOf('TestsRepository', $this->m->xyz);
		$this->assertSame($this->m->tests, $this->m->xyz);
		$this->assertSame($this->m->TestsRepository, $this->m->xyz);
	}

	public function testMore()
	{
		$this->m->register('xyz', 'TestsRepository');
		$this->m->register('abc', 'TestsRepository');
		$this->assertInstanceOf('TestsRepository', $this->m->xyz);
		$this->assertInstanceOf('TestsRepository', $this->m->abc);
		$this->assertSame($this->m->tests, $this->m->xyz);
		$this->assertSame($this->m->tests, $this->m->abc);
		$this->assertSame($this->m->xyz, $this->m->abc);
	}

	public function testOldBefore1()
	{
		$t = $this->m->tests;
		$this->m->register('xyz', 'TestsRepository');
		$this->assertSame($this->m->tests, $this->m->xyz);
		$this->assertSame($t, $this->m->xyz);
		$this->assertSame($t, $this->m->tests);
	}

	public function testOldBefore2()
	{
		$this->m->register('xyz', 'TestsRepository');
		$t = $this->m->tests;
		$this->assertSame($this->m->tests, $this->m->xyz);
		$this->assertSame($t, $this->m->xyz);
		$this->assertSame($t, $this->m->tests);
	}


	public function testOldBefore3()
	{
		$t = $this->m->testsRepository;
		$this->m->register('xyz', 'TestsRepository');
		$this->assertSame($this->m->testsRepository, $this->m->xyz);
		$this->assertSame($t, $this->m->xyz);
		$this->assertSame($t, $this->m->testsRepository);
	}

	public function testOldBefore4()
	{
		$this->m->register('xyz', 'TestsRepository');
		$t = $this->m->testsRepository;
		$this->assertSame($this->m->testsRepository, $this->m->xyz);
		$this->assertSame($t, $this->m->xyz);
		$this->assertSame($t, $this->m->testsRepository);
	}


	public function testOldAfter()
	{
		$this->m->register('xyz', 'TestsRepository');
		$this->assertSame($this->m->tests, $this->m->xyz);
	}

	public function testOldAfter2()
	{
		$this->m->register('xyz', 'TestsRepository');
		$this->assertSame($this->m->testsRepository, $this->m->xyz);
	}

	public function testBad()
	{
		$this->setExpectedException('Orm\RepositoryNotFoundException', "Repository 'Directory' must implement Orm\\IRepository");
		$this->m->register('xyz', 'Directory');
	}

	public function testExists()
	{
		$this->m->register('xyz', 'TestsRepository');
		$this->setExpectedException('Orm\RepositoryAlreadyRegisteredException', "Repository alias 'xyz' is already registered");
		$this->m->register('xyz', 'TestsRepository');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RepositoryContainer', 'register');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
