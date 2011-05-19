<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../boot.php';

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
	}

	public function testSingleton()
	{
		$this->assertSame($this->m->getRepository('tests'), $this->m->getRepository('tests'));
	}

	public function testGet()
	{
		$this->assertInstanceOf('TestsRepository', $this->m->tests);
		$this->assertSame($this->m->getRepository('tests'), $this->m->tests);
	}

	public function testNamespace()
	{
		$this->assertInstanceOf('RepositoryContainer_namespace\RepositoryContainer_namespaceRepository', $this->m->{'RepositoryContainer_namespace\RepositoryContainer_namespace'});
		$this->assertSame($this->m->getRepository('RepositoryContainer_namespace\RepositoryContainer_namespace'), $this->m->{'RepositoryContainer_namespace\RepositoryContainer_namespace'});
	}

	public function testNamespaceRegister()
	{
		$this->m->register('rcn', 'RepositoryContainer_namespace\RepositoryContainer_namespaceRepository');
		$this->assertInstanceOf('RepositoryContainer_namespace\RepositoryContainer_namespaceRepository', $this->m->rcn);
		$this->assertSame($this->m->getRepository('rcn'), $this->m->rcn);
		$this->assertSame($this->m->getRepository('rcn'), $this->m->getRepository('RepositoryContainer_namespace\RepositoryContainer_namespace'));
	}
}
