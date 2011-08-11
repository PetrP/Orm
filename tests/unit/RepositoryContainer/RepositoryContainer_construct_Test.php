<?php

use Orm\RepositoryContainer;


/**
 * @covers Orm\RepositoryContainer::__construct
 */
class RepositoryContainer_construct_Test extends TestCase
{

	public function test()
	{
		$this->assertInstanceOf('Orm\IRepositoryContainer', new RepositoryContainer);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RepositoryContainer', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
