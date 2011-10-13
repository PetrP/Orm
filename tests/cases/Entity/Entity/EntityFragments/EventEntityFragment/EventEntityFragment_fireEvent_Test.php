<?php

/**
 * @covers Orm\EventEntityFragment::fireEvent
 */
class EventEntityFragment_fireEvent_Test extends EventEntityFragment_event_Base
{

	public function testNoParam()
	{
		$this->e->fireEvent('onCreate');
		$this->assertSame('onCreate', $this->e->event);
		$this->assertSame(array(NULL), $this->e->eventParam);
	}

	public function testRepository()
	{
		$this->e->fireEvent('onBeforePersist', $this->r);
		$this->assertSame('onBeforePersist', $this->e->event);
		$this->assertSame(array($this->r), $this->e->eventParam);
	}

	public function testMore()
	{
		$this->e->fireEvent('onLoad', $this->r, array('x', 'id' => 1));
		$this->assertSame('onLoad', $this->e->event);
		$this->assertSame(array($this->r, array('x', 'id' => 1)), $this->e->eventParam);
	}

	public function testBadEvent()
	{
		$e = new EventEntityFragment2_Entity;
		$this->setExpectedException('Orm\InvalidEntityException', 'Call to undefined event EventEntityFragment2_Entity::onBad().');
		$e->fireEvent('onBad');
	}

	public function testUserDefined()
	{
		$e = new EventEntityFragment2_Entity;
		$this->setExpectedException('Orm\InvalidEntityException', 'Call to undefined event EventEntityFragment2_Entity::onUserDefined().');
		$e->fireEvent('onUserDefined');
	}

	public function testNoParent()
	{
		$e = new EventEntityFragment2_Entity;
		$this->setExpectedException('Orm\InvalidEntityException', 'Method EventEntityFragment2_Entity::onBeforePersist() or its descendant doesn\'t call parent::onBeforePersist().');
		$e->fireEvent('onBeforePersist', $this->r);
	}

	public function testBadParent()
	{
		$e = new EventEntityFragment2_Entity;
		$this->setExpectedException('Orm\InvalidEntityException', 'Method EventEntityFragment2_Entity::onAfterPersist() or its descendant doesn\'t call parent::onAfterPersist().');
		$e->fireEvent('onAfterPersist', $this->r);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EventEntityFragment', 'fireEvent');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
