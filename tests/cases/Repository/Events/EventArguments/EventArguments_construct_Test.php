<?php

use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::__construct
 */
class EventArguments_construct_Test extends TestCase
{
	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EventArguments', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public static function dataProviderTypes()
	{
		return array_map(function ($t) { return array($t); }, array(
			Events::LOAD,
			Events::ATTACH,
			Events::PERSIST_BEFORE,
			Events::PERSIST_BEFORE_UPDATE,
			Events::PERSIST_BEFORE_INSERT,
			Events::PERSIST,
			Events::PERSIST_AFTER_UPDATE,
			Events::PERSIST_AFTER_INSERT,
			Events::PERSIST_AFTER,
			Events::REMOVE_BEFORE,
			Events::REMOVE_AFTER,
		));
	}

}
