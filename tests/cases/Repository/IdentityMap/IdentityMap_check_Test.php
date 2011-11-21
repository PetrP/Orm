<?php

use Orm\RepositoryContainer;
use Orm\IdentityMap;

/**
 * @covers Orm\IdentityMap::check
 * @see Repository_isAttachableEntity_Test
 */
class IdentityMap_check_Test extends TestCase
{

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\IdentityMap', 'check');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
