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

}
