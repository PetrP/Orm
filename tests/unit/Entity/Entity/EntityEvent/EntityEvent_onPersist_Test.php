<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\_EntityEvent::onAttach
 * @covers Orm\_EntityEvent::onBeforePersist
 * @covers Orm\_EntityEvent::onBeforeInsert
 * @covers Orm\_EntityEvent::onBeforeUpdate
 * @covers Orm\_EntityEvent::onPersist
 * @covers Orm\_EntityEvent::onAfterUpdate
 * @covers Orm\_EntityEvent::onAfterInsert
 * @covers Orm\_EntityEvent::onAfterPersist
 */
class EntityEvent_onPersist_Test extends EntityEvent_event_Base
{

	public function testInsert()
	{
		$this->assertSame(false, isset($this->e->id));
		$this->r->persist($this->e);
		$this->assertSame(array(
			array('onAttach', array($this->r)),
			array('onBeforePersist', array($this->r)),
			array('onBeforeInsert', array($this->r)),
			array('onAfterInsert', array($this->r)),
			array('onAfterPersist', array($this->r)),
		), $this->e->all);
		$this->assertSame(true, isset($this->e->id));
		$this->assertSame(2, $this->e->id);
	}

	public function testUpdate()
	{
		$this->e = $this->r->getById(1);
		$this->e->var = ''; // changed
		$this->assertSame(1, $this->e->id);
		$this->r->persist($this->e);
		$this->assertSame(array(
			array('onBeforePersist', array($this->r)),
			array('onBeforeUpdate', array($this->r)),
			array('onAfterUpdate', array($this->r)),
			array('onAfterPersist', array($this->r)),
		), $this->e->all);
		$this->assertSame(1, $this->e->id);
	}

}
