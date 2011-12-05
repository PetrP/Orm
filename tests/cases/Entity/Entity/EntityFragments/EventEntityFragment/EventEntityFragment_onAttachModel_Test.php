<?php

/**
 * @covers Orm\EventEntityFragment::onAttachModel
 * @covers Orm\EventEntityFragment::fireEvent
 */
class EventEntityFragment_onAttachModel_Test extends EventEntityFragment_event_Base
{

	private $r2;
	protected function setUp()
	{
		parent::setUp();
		$this->r2 = $this->r->model->EventEntityFragment_onAttachModel_Repository;
	}

	public function test()
	{
		$e = new EventEntityFragment_onAttachModel_Entity;

		$this->assertSame(NULL, $e->getRepository(false));
		$this->assertSame(NULL, $e->getModel(false));

		$e->fireEvent('onAttachModel', NULL, $this->r2->model);

		$this->assertSame(NULL, $e->getRepository(false));
		$this->assertSame($this->r2->model, $e->getModel(false));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EventEntityFragment', 'onAttachModel');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
