<?php

/**
 * @covers Orm\ManyToMany::count
 */
class ManyToMany_count_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->assertSame(4, $this->m2m->count());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseToMany', 'count');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
