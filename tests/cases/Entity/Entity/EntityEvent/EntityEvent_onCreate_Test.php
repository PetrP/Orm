<?php

/**
 * @covers Orm\_EntityEvent::__construct
 * @covers Orm\_EntityEvent::onCreate
 */
class EntityEvent_onCreate_Test extends EntityEvent_event_Base
{

	public function testConstructor()
	{
		$this->assertSame('onCreate', $this->e->event);
		$this->assertSame(array(NULL), $this->e->eventParam);
	}

}
