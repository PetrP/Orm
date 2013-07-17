<?php

/**
 * @covers Orm\DibiResultWrapper::fetchPairs
 */
class DibiResultWrapper_fetchPairs_Test extends DibiResultWrapper_Base_Test
{

	public function test1()
	{
		$this->assertSame(array(1 => 1, 2 => 2), $this->w->fetchPairs('id', 'id'));
		$this->assertSame('fetch#0 fetch#1 fetch#2', implode(' ', $this->d->operations));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiResultWrapper', 'fetchPairs');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
