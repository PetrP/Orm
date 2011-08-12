<?php

/**
 * @covers Orm\EventEntityFragment::__construct
 * @covers Orm\EventEntityFragment::onCreate
 */
class EventEntityFragment_onCreate_Test extends EventEntityFragment_event_Base
{

	public function testConstructor()
	{
		$this->assertSame('onCreate', $this->e->event);
		$this->assertSame(array(NULL), $this->e->eventParam);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EventEntityFragment', 'onCreate');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
