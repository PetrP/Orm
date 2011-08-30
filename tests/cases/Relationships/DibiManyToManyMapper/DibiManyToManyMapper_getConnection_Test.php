<?php

/**
 * @covers Orm\DibiManyToManyMapper::getConnection
 */
class DibiManyToManyMapper_getConnection_Test extends TestCase
{

	public function test()
	{
		$c = new DibiConnection(array('lazy' => true));
		$m = new DibiManyToManyMapper_getConnection_DibiManyToManyMapper($c);
		$this->assertSame($c, $m->__getConnection());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiManyToManyMapper', 'getConnection');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
