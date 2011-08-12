<?php

use Orm\DibiManyToManyMapper;

/**
 * @covers Orm\DibiManyToManyMapper::__construct
 */
class DibiManyToManyMapper_construct_Test extends TestCase
{
	public function test()
	{
		$c = new DibiConnection(array('lazy' => true));
		$mm = new DibiManyToManyMapper($c);
		$this->assertInstanceOf('Orm\IManyToManyMapper', $mm);
		$this->assertAttributeSame($c, 'connection', $mm);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiManyToManyMapper', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
