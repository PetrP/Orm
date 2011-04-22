<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers _EntityEvent::___event
 */
class EntityEvent_event_Test extends EntityEvent_event_Base
{

	public function testNoParam()
	{
		$this->e->___event($this->e, 'create');
		$this->assertSame('onCreate', $this->e->event);
		$this->assertSame(array(NULL), $this->e->eventParam);
	}

	public function testRepository()
	{
		$this->e->___event($this->e, 'beforePersist', $this->r);
		$this->assertSame('onBeforePersist', $this->e->event);
		$this->assertSame(array($this->r), $this->e->eventParam);
	}

	public function testMore()
	{
		$this->e->___event($this->e, 'load', $this->r, array('x'));
		$this->assertSame('onLoad', $this->e->event);
		$this->assertSame(array($this->r, array('x')), $this->e->eventParam);
	}

	public function testBadEvent()
	{
		$e = new EntityEvent2_Entity;
		$this->setExpectedException('InvalidArgumentException', 'Call to undefined event EntityEvent2_Entity::onBad().');
		$e->___event($e, 'bad');
	}

	public function testUserDefined()
	{
		$e = new EntityEvent2_Entity;
		$this->setExpectedException('InvalidArgumentException', 'Call to undefined event EntityEvent2_Entity::onUserDefined().');
		$e->___event($e, 'userDefined');
	}

	public function testNoParent()
	{
		$e = new EntityEvent2_Entity;
		$this->setExpectedException('InvalidStateException', 'Method EntityEvent2_Entity::onBeforePersist() or its descendant doesn\'t call parent::onBeforePersist().');
		$e->___event($e, 'beforePersist', $this->r);
	}

	public function testBadParent()
	{
		$e = new EntityEvent2_Entity;
		$this->setExpectedException('InvalidStateException', 'Method EntityEvent2_Entity::onAfterPersist() or its descendant doesn\'t call parent::onAfterPersist().');
		$e->___event($e, 'afterPersist', $this->r);
	}
}
