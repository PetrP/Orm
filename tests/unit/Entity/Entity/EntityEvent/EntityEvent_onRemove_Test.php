<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\_EntityEvent::onBeforeRemove
 * @covers Orm\_EntityEvent::onAfterRemove
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
