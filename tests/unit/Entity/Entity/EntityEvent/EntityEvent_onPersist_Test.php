<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers _EntityEvent::onBeforePersist
 * @covers _EntityEvent::onBeforeInsert
 * @covers _EntityEvent::onBeforeUpdate
 * @covers _EntityEvent::onPersist
 * @covers _EntityEvent::onAfterUpdate
 * @covers _EntityEvent::onAfterInsert
 * @covers _EntityEvent::onAfterPersist
 */
class EntityEvent_onPersist_Test extends EntityEvent_event_Base
{

	public function testInsert()
	{
		$this->assertSame(false, isset($this->e->id));
		$this->r->persist($this->e);
		$this->assertSame(array(
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
