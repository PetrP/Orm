<?php

/**
 * @covers Orm\_EntityEvent::onAttach
 * @see EntityEvent_onPersist_Test
 */
class EntityEvent_onAttach_Test extends EntityEvent_event_Base
{

	public function testNew()
	{
		$this->r->attach($this->e);
		$this->assertSame(array(
			array('onAttach', array($this->r)),
		), $this->e->all);
		$this->e->all = array();
		$this->r->attach($this->e);
		$this->assertSame(array(), $this->e->all);
		$this->assertSame(false, isset($this->e->id));
	}

	public function testAlreadyAttached()
	{
		$e = $this->r->getById(1);
		$this->r->attach($e);
		$this->assertSame(array(), $e->all);
	}

}
