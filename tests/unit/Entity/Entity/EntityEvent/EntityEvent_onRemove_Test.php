<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers _EntityEvent::onBeforeRemove
 * @covers _EntityEvent::onAfterRemove
 */
class EntityEvent_onRemove_Test extends EntityEvent_event_Base
{

	public function testNew()
	{
		$this->assertSame(false, isset($this->e->id));
		$this->r->remove($this->e);
		$this->assertSame(array(
			array('onBeforeRemove', array($this->r)),
			array('onAfterRemove', array($this->r)),
		), $this->e->all);
		$this->assertSame(false, isset($this->e->id));
	}

	public function testPersisted()
	{
		$this->e = $this->r->getById(1);
		$this->assertSame(1, $this->e->id);
		$this->r->remove($this->e);
		$this->assertSame(array(
			array('onBeforeRemove', array($this->r)),
			array('onAfterRemove', array($this->r)),
		), $this->e->all);
		$this->assertSame(false, isset($this->e->id));
	}

}
