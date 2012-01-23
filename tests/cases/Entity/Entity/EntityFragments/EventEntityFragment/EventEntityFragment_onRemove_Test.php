<?php

/**
 * @covers Orm\EventEntityFragment::onBeforeRemove
 * @covers Orm\EventEntityFragment::onAfterRemove
 */
class EventEntityFragment_onRemove_Test extends EventEntityFragment_event_Base
{

	public function testNew()
	{
		$this->assertSame(false, isset($this->e->id));
		$this->r->remove($this->e);
		$this->assertSame(array(), $this->e->all);
		$this->assertSame(false, isset($this->e->id));
	}

	public function testNewAttached()
	{
		$this->r->attach($this->e);
		$this->e->all = array();
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
