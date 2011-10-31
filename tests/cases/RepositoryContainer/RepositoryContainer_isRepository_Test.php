<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\RepositoryContainer::isRepository
 */
class RepositoryContainer_isRepository_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
	}

	public function test()
	{
		$this->assertTrue($this->m->isRepository('tests'));
		$this->assertTrue($this->m->isRepository('testsRepository'));
		$this->assertTrue($this->m->isRepository('tests'));
		$this->assertTrue($this->m->isRepository('testsRepository'));
	}

	public function test2()
	{
		$this->assertTrue($this->m->isRepository('TestS'));
		$this->assertTrue($this->m->isRepository('TestSRepositoRY'));
		$this->assertTrue($this->m->isRepository('tESTs'));
		$this->assertTrue($this->m->isRepository('tESTsRePosITory'));
	}

	public function test3()
	{
		$this->assertFalse($this->m->isRepository('blabla'));
		$this->assertFalse($this->m->isRepository('blablaRepository'));
		$this->assertFalse($this->m->isRepository('Xyz'));
		$this->assertFalse($this->m->isRepository('XyzRepository'));
	}

	public function testBC()
	{
		$this->assertFalse($this->m->isRepository('tests', false));
		$this->assertTrue($this->m->isRepository('testsRepository', false));

		$this->assertTrue($this->m->isRepository('tests', true));
		$this->assertTrue($this->m->isRepository('testsRepository', true));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RepositoryContainer', 'isRepository');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
