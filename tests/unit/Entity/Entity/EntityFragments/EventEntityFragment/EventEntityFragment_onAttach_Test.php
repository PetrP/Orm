<?php

/**
 * @covers Orm\EventEntityFragment::onAttach
 * @see EventEntityFragment_onPersist_Test
 */
class EventEntityFragment_onAttach_Test extends EventEntityFragment_event_Base
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

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EventEntityFragment', 'onAttach');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
