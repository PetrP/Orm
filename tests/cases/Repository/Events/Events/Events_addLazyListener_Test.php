<?php

use Orm\Events;
use Orm\RepositoryContainer;
use Orm\Callback;

/**
 * @covers Orm\Events::addLazyListener
 */
class Events_addLazyListener_Test extends Events_TestCase
{
	private $e;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->e = new Events($m->tests);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Events', 'addLazyListener');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function testReturn()
	{
		$r = $this->e->addLazyListener(Events::ATTACH, function () {});
		$this->assertSame($this->e, $r);
	}

	public function testNoEvent()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\Events::addLazyListener() no event constant match for '0'.");
		$this->e->addLazyListener(0, function () {});
	}

	/**
	 * @dataProvider dataProviderAll
	 */
	public function testAll($event)
	{
		$listenersInit = $this->readAttribute($this->e, 'listeners');

		$cb = array($this, 'testAll');
		$this->e->addLazyListener($event, $cb);

		$listenersUnset = $listeners = $this->readAttribute($this->e, 'listeners');
		unset($listenersUnset[$event][0]);
		$this->assertSame($listenersInit, $listenersUnset);

		$this->assertSame(array(false, 0), $listeners[$event][0]);
		$this->assertSame(array(
			0 => array($cb, array($event => 0))
		), $this->readAttribute($this->e, 'lazy'));
	}

	/**
	 * @dataProvider dataProviderCallbacks
	 */
	public function testCallbacks($cb, $expect)
	{
		$this->e->addLazyListener(Events::HYDRATE_BEFORE, $cb);

		$listeners = $this->readAttribute($this->e, 'listeners');
		$this->assertSame(array(false, 0), $listeners[Events::HYDRATE_BEFORE][0]);
		$this->assertSame(array(
			0 => array($expect, array(Events::HYDRATE_BEFORE => 0))
		), $this->readAttribute($this->e, 'lazy'));
	}

	public function dataProviderCallbacks()
	{
		return array(
			'array' => array(array($this, 'testAll'), array($this, 'testAll')),
			'string' => array('strtolower', 'strtolower'),
			'closure' => array($f = function () {}, $f),
			'create_function' => array($f = create_function('', ''), $f),
			'Nette' => array($f = callback($this, 'testAll'), array($this, 'testAll')),
			'Orm' => array($f = Callback::create($this, 'testAll'), array($this, 'testAll')),
			'invoke' => array($o = new Events_invoke, array($o, '__invoke')),
			'static' => array('Events_addLazyListener_Test::cb', array('Events_addLazyListener_Test', 'cb')),
		);
	}

	public function testMore()
	{
		$this->e->addCallbackListener(Events::HYDRATE_BEFORE | Events::ATTACH | Events::REMOVE_AFTER, function () {});
		$this->e->addCallbackListener(Events::HYDRATE_BEFORE, function () {});

		$listenersInit = $this->readAttribute($this->e, 'listeners');

		$cb = array($this, 'testAll');
		$this->e->addLazyListener(Events::HYDRATE_BEFORE | Events::ATTACH | Events::REMOVE_AFTER, $cb);

		$cb2 = array($this, 'testMore');
		$this->e->addLazyListener(Events::HYDRATE_BEFORE | Events::ATTACH | Events::REMOVE_AFTER, $cb2);

		$listenersUnset = $listeners = $this->readAttribute($this->e, 'listeners');
		unset($listenersUnset[Events::HYDRATE_BEFORE][2]);
		unset($listenersUnset[Events::HYDRATE_BEFORE][3]);
		unset($listenersUnset[Events::ATTACH][1]);
		unset($listenersUnset[Events::ATTACH][2]);
		unset($listenersUnset[Events::REMOVE_AFTER][1]);
		unset($listenersUnset[Events::REMOVE_AFTER][2]);
		$this->assertSame($listenersInit, $listenersUnset);

		$this->assertSame(array(false, 0), $listeners[Events::HYDRATE_BEFORE][2]);
		$this->assertSame(array(false, 0), $listeners[Events::ATTACH][1]);
		$this->assertSame(array(false, 0), $listeners[Events::REMOVE_AFTER][1]);

		$this->assertSame(array(false, 1), $listeners[Events::HYDRATE_BEFORE][3]);
		$this->assertSame(array(false, 1), $listeners[Events::ATTACH][2]);
		$this->assertSame(array(false, 1), $listeners[Events::REMOVE_AFTER][2]);

		$this->assertSame(array(
			0 => array($cb, array(Events::HYDRATE_BEFORE => 2, Events::ATTACH => 1, Events::REMOVE_AFTER => 1)),
			1 => array($cb2, array(Events::HYDRATE_BEFORE => 3, Events::ATTACH => 2, Events::REMOVE_AFTER => 2)),
		), $this->readAttribute($this->e, 'lazy'));
	}

	public function testInvalidCallback()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', 'Invalid callback.');
		$this->e->addLazyListener(0, array());
	}

}
