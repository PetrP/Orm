<?php

/**
 * @covers Orm\EventEntityFragment::onLoad
 */
class EventEntityFragment_onLoad_Test extends EventEntityFragment_event_Base
{

	public function testGetById()
	{
		$e = $this->r->getById(1);
		$this->assertSame('onLoad', $e->event);
		$this->assertSame(array($this->r, array('id' => 1)), $e->eventParam);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EventEntityFragment', 'onLoad');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
