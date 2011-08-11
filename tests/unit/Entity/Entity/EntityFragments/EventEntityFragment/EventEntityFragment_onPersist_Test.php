<?php

/**
 * @covers Orm\EventEntityFragment::onAttach
 * @covers Orm\EventEntityFragment::onBeforePersist
 * @covers Orm\EventEntityFragment::onBeforeInsert
 * @covers Orm\EventEntityFragment::onBeforeUpdate
 * @covers Orm\EventEntityFragment::onPersist
 * @covers Orm\EventEntityFragment::onAfterUpdate
 * @covers Orm\EventEntityFragment::onAfterInsert
 * @covers Orm\EventEntityFragment::onAfterPersist
 */
class EventEntityFragment_onPersist_Test extends EventEntityFragment_event_Base
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

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EventEntityFragment', 'onPersist');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
