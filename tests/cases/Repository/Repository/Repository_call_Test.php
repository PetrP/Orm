<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::__call
 */
class Repository_call_Test extends TestCase
{
	/** @var Repository_call_Repository */
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->{'Repository_call_Repository'};
	}

	public function test()
	{
		$this->assertInstanceOf('Orm\ArrayCollection', $this->r->findById(1));
		$this->assertSame(array(1 => 1), $this->r->findById(1)->fetchPairs('id', 'id'));
	}

	public function testUndefined()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Call to undefined method Repository_call_Repository::findByIdx().');
		$this->r->findByIdx(1);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', '__call');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
