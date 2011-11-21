<?php

use Orm\RepositoryContainer;
use Orm\IdentityMap;

/**
 * @covers Orm\IdentityMap::create
 * @see Repository_hydrateEntity_Test
 * @see Repository_hydrateEntity_events_Test
 */
class IdentityMap_create_Test extends TestCase
{

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\IdentityMap', 'create');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
