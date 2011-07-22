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

}
