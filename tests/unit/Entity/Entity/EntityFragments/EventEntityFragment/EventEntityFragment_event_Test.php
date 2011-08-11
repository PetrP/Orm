<?php

/**
 * @covers Orm\EventEntityFragment::___event
 */
class EventEntityFragment_event_Test extends EventEntityFragment_event_Base
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
		$this->e->___event($this->e, 'load', $this->r, array('x', 'id' => 1));
		$this->assertSame('onLoad', $this->e->event);
		$this->assertSame(array($this->r, array('x', 'id' => 1)), $this->e->eventParam);
	}

	public function testBadEvent()
	{
		$e = new EventEntityFragment2_Entity;
		$this->setExpectedException('InvalidArgumentException', 'Call to undefined event EventEntityFragment2_Entity::onBad().');
		$e->___event($e, 'bad');
	}

	public function testUserDefined()
	{
		$e = new EventEntityFragment2_Entity;
		$this->setExpectedException('InvalidArgumentException', 'Call to undefined event EventEntityFragment2_Entity::onUserDefined().');
		$e->___event($e, 'userDefined');
	}

	public function testNoParent()
	{
		$e = new EventEntityFragment2_Entity;
		$this->setExpectedException('Nette\InvalidStateException', 'Method EventEntityFragment2_Entity::onBeforePersist() or its descendant doesn\'t call parent::onBeforePersist().');
		$e->___event($e, 'beforePersist', $this->r);
	}

	public function testBadParent()
	{
		$e = new EventEntityFragment2_Entity;
		$this->setExpectedException('Nette\InvalidStateException', 'Method EventEntityFragment2_Entity::onAfterPersist() or its descendant doesn\'t call parent::onAfterPersist().');
		$e->___event($e, 'afterPersist', $this->r);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EventEntityFragment', '___event');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
