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

	public function testOld1()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository alias 'tests' is already registered");
		$this->m->register('tests', 'TestsRepository');
	}

	public function testAlias()
	{
		$this->m->register('xyz', 'TestsRepository');
		$this->assertInstanceOf('TestsRepository', $this->m->xyz);
		$this->assertSame($this->m->tests, $this->m->xyz);
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

	public function testOldAfter()
	{
		$this->m->register('xyz', 'TestsRepository');
		$this->assertSame($this->m->tests, $this->m->xyz);
	}

	public function testBad()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository 'Nette\\Utils\\Html' must implement Orm\\IRepository");
		$this->m->register('xyz', 'Nette\Utils\Html');
	}

	public function testExists()
	{
		$this->m->register('xyz', 'TestsRepository');
		$this->setExpectedException('Nette\InvalidStateException', "Repository alias 'xyz' is already registered");
		$this->m->register('xyz', 'TestsRepository');
	}
}
