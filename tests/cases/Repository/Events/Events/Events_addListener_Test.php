<?php

use Orm\Events;
use Orm\RepositoryContainer;

/**
 * @covers Orm\Events::addListener
 */
class Events_addListener_Test extends TestCase
{
	private $e;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->e = new Events($m->tests);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Events', 'addListener');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function testReturn()
	{
		$r = $this->e->addListener(new Events_addListener_Attach);
		$this->assertSame($this->e, $r);
	}

	public function testJustListenerNoEvent()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\Events::addListener() no event interface match for 'Events_addListener_Event'.");
		$this->e->addListener(new Events_addListener_Event);
	}

	/**
	 * @dataProvider dataProviderInterface
	 */
	public function testInterface($className, $event, $method)
	{
		$listenersInit = $this->readAttribute($this->e, 'listeners');

		$object = new $className;
		$this->e->addListener($object);

		$listenersUnset = $listeners = $this->readAttribute($this->e, 'listeners');
		unset($listenersUnset[$event][0]);
		$this->assertSame($listenersInit, $listenersUnset);

		$this->assertSame(array(true, array($object, $method)), $listeners[$event][0]);
	}

	public function dataProviderInterface()
	{
		$r = array();
		foreach (array(
			array('Events_addListener_Hydrate_before', Events::HYDRATE_BEFORE, 'onBeforeHydrateEvent'),
			array('Events_addListener_Hydrate_after', Events::HYDRATE_AFTER, 'onAfterHydrateEvent'),
			array('Events_addListener_Attach', Events::ATTACH, 'onAttachEvent'),
			array('Events_addListener_Persist_before', Events::PERSIST_BEFORE, 'onBeforePersistEvent'),
			array('Events_addListener_Persist_before_update', Events::PERSIST_BEFORE_UPDATE, 'onBeforePersistUpdateEvent'),
			array('Events_addListener_Persist_before_insert', Events::PERSIST_BEFORE_INSERT, 'onBeforePersistInsertEvent'),
			array('Events_addListener_Persist', Events::PERSIST, 'onPersistEvent'),
			array('Events_addListener_Persist_after_update', Events::PERSIST_AFTER_UPDATE, 'onAfterPersistUpdateEvent'),
			array('Events_addListener_Persist_after_insert', Events::PERSIST_AFTER_INSERT, 'onAfterPersistInsertEvent'),
			array('Events_addListener_Persist_after', Events::PERSIST_AFTER, 'onAfterPersistEvent'),
			array('Events_addListener_Remove_before', Events::REMOVE_BEFORE, 'onBeforeRemoveEvent'),
			array('Events_addListener_Remove_after', Events::REMOVE_AFTER, 'onAfterRemoveEvent'),
			array('Events_addListener_Flush_before', Events::FLUSH_BEFORE, 'onBeforeFlushEvent'),
			array('Events_addListener_Flush_after', Events::FLUSH_AFTER, 'onAfterFlushEvent'),
			array('Events_addListener_Clean_before', Events::CLEAN_BEFORE, 'onBeforeCleanEvent'),
			array('Events_addListener_Clean_after', Events::CLEAN_AFTER, 'onAfterCleanEvent'),
			array('Events_addListener_Serialize_before', Events::SERIALIZE_BEFORE, 'onBeforeSerializeEvent'),
			array('Events_addListener_Serialize_after', Events::SERIALIZE_AFTER, 'onAfterSerializeEvent'),
			array('Events_addListener_Serialize_conventional', Events::SERIALIZE_CONVENTIONAL, 'onConventionalSerializeEvent'),

		) as $tmp) $r[$tmp[0]] = $tmp;
		return $r;
	}

	public function testMore()
	{
		$listenersInit = $this->readAttribute($this->e, 'listeners');

		$object = new Events_addListener_Remove;
		$this->e->addListener($object);

		$listenersUnset = $listeners = $this->readAttribute($this->e, 'listeners');
		unset($listenersUnset[Events::REMOVE_BEFORE][0]);
		unset($listenersUnset[Events::REMOVE_AFTER][0]);
		$this->assertSame($listenersInit, $listenersUnset);

		$this->assertSame(array(true, array($object, 'onBeforeRemoveEvent')), $listeners[Events::REMOVE_BEFORE][0]);
		$this->assertSame(array(true, array($object, 'onAfterRemoveEvent')), $listeners[Events::REMOVE_AFTER][0]);
	}

}
