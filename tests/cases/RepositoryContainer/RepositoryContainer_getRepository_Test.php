<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\RepositoryContainer::getRepository
 * @covers Orm\RepositoryContainer::__get
 */
class RepositoryContainer_getRepository_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
	}

	public function test()
	{
		$this->assertInstanceOf('TestsRepository', $this->m->getRepository('tests'));
		$this->assertInstanceOf('TestsRepository', $this->m->getRepository('TestsRepository'));
	}

	public function testSingleton()
	{
		$this->assertSame($this->m->getRepository('tests'), $this->m->getRepository('tests'));
		$this->assertSame($this->m->getRepository('TestsRepository'), $this->m->getRepository('TestsRepository'));
		$this->assertSame($this->m->getRepository('TestsRepository'), $this->m->getRepository('tests'));
	}

	public function testGet()
	{
		$this->assertInstanceOf('TestsRepository', $this->m->tests);
		$this->assertInstanceOf('TestsRepository', $this->m->testsRepository);
		$this->assertSame($this->m->getRepository('tests'), $this->m->tests);
		$this->assertSame($this->m->getRepository('TestsRepository'), $this->m->testsRepository);
		$this->assertSame($this->m->tests, $this->m->testsRepository);
	}

	public function testNamespace()
	{
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestIncomplete('php 5.2 (namespace)');
		}
		$this->assertInstanceOf('RepositoryContainer_namespace\RepositoryContainer_namespaceRepository', $this->m->{'RepositoryContainer_namespace\RepositoryContainer_namespace'});
		$this->assertInstanceOf('RepositoryContainer_namespace\RepositoryContainer_namespaceRepository', $this->m->{'RepositoryContainer_namespace\RepositoryContainer_namespaceRepository'});
		$this->assertSame($this->m->getRepository('RepositoryContainer_namespace\RepositoryContainer_namespace'), $this->m->{'RepositoryContainer_namespace\RepositoryContainer_namespace'});
		$this->assertSame($this->m->getRepository('RepositoryContainer_namespace\RepositoryContainer_namespaceRepository'), $this->m->{'RepositoryContainer_namespace\RepositoryContainer_namespaceRepository'});
		$this->assertSame($this->m->getRepository('RepositoryContainer_namespace\RepositoryContainer_namespace'), $this->m->{'RepositoryContainer_namespace\RepositoryContainer_namespaceRepository'});
	}

	public function testNamespaceRegister()
	{
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestIncomplete('php 5.2 (namespace)');
		}
		$this->m->register('rcn', 'RepositoryContainer_namespace\RepositoryContainer_namespaceRepository');
		$this->assertInstanceOf('RepositoryContainer_namespace\RepositoryContainer_namespaceRepository', $this->m->rcn);
		$this->assertSame($this->m->getRepository('rcn'), $this->m->rcn);
		$this->assertSame($this->m->getRepository('rcn'), $this->m->getRepository('RepositoryContainer_namespace\RepositoryContainer_namespace'));
		$this->assertSame($this->m->getRepository('rcn'), $this->m->getRepository('RepositoryContainer_namespace\RepositoryContainer_namespaceRepository'));
	}

	public function testFreezeContext()
	{
		$this->assertAttributeSame(false, 'frozen', $this->m->getContext());
		$this->m->tests;
		$this->assertAttributeSame(true, 'frozen', $this->m->getContext());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RepositoryContainer', 'getRepository');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
