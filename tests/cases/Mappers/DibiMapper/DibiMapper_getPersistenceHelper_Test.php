<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiMapper::getPersistenceHelper
 */
class DibiMapper_getPersistenceHelper_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new DibiMapper_getPersistenceHelper_DibiMapper(new TestsRepository(new RepositoryContainer));
	}

	public function test()
	{
		$ph = $this->m->__getPersistenceHelper();
		$this->assertInstanceOf('Orm\DibiPersistenceHelper', $ph);
		$this->assertAttributeInstanceOf('DibiConnection', 'connection', $ph);
		$this->assertAttributeSame($this->m->getConnection(), 'connection', $ph);
		$this->assertAttributeInstanceOf('Orm\SqlConventional', 'conventional', $ph);
		$this->assertAttributeSame($this->m->getConventional(), 'conventional', $ph);
		$this->assertAttributeSame('tests', 'table', $ph);
		$this->assertAttributeSame($this->m->repository->events, 'events', $ph);
	}

	public function testNoCache()
	{
		$this->assertNotSame($this->m->__getPersistenceHelper(), $this->m->__getPersistenceHelper());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'getPersistenceHelper');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
