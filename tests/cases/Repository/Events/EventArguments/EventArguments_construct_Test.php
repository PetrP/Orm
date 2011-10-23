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
		return Events_addCallbackListener_Test::dataProviderAll();
	}

}
