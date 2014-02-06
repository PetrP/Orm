<?php

/**
 * @covers Orm\DibiResultWrapper::count
 */
class DibiResultWrapper_count_Test extends DibiResultWrapper_Base_Test
{

	public function test()
	{
		$this->assertSame(2, $this->w->count());
		$this->assertSame(2, count($this->w));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiResultWrapper', 'count');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
