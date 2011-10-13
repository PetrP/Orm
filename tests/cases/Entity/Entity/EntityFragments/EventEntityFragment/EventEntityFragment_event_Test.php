<?php

/**
 * @covers Orm\EventEntityFragment::___event
 */
class EventEntityFragment_event_Test extends EventEntityFragment_event_Base
{

	public function testDeprecated()
	{
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\Entity::___event() is deprecated; use Orm\Entity::fireEvent() instead');
		$this->e->___event($this->e, 'create');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EventEntityFragment', '___event');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
