<?php

use Orm\Events;
use Orm\RepositoryContainer;
use Orm\Callback;

/**
 * @covers Orm\Events::addCallbackListener
 */
class Events_addCallbackListener_Test extends TestCase
{
	private $e;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->e = new Events($m->tests);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Events', 'addCallbackListener');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function testReturn()
	{
		$r = $this->e->addCallbackListener(Events::ATTACH, function () {});
		$this->assertSame($this->e, $r);
	}

	public function testNoEvent()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\Events::addCallbackListener() no event constant match for '0'.");
		$this->e->addCallbackListener(0, function () {});
	}
	/**
	 * @dataProvider dataProviderAll
	 */
	public function testAll($event)
	{
		$listenersInit = $this->readAttribute($this->e, 'listeners');

		$cb = array($this, 'testAll');
		$this->e->addCallbackListener($event, $cb);

		$listenersUnset = $listeners = $this->readAttribute($this->e, 'listeners');
		unset($listenersUnset[$event][0]);
		$this->assertSame($listenersInit, $listenersUnset);

		$this->assertSame(array(true, $cb), $listeners[$event][0]);
	}

	public static function dataProviderAll()
	{
		return array(
			array(Events::LOAD_BEFORE),
			array(Events::LOAD_AFTER),
			array(Events::ATTACH),
			array(Events::PERSIST_BEFORE),
			array(Events::PERSIST_BEFORE_UPDATE),
			array(Events::PERSIST_BEFORE_INSERT),
			array(Events::PERSIST),
			array(Events::PERSIST_AFTER_UPDATE),
			array(Events::PERSIST_AFTER_INSERT),
			array(Events::PERSIST_AFTER),
			array(Events::REMOVE_BEFORE),
			array(Events::REMOVE_AFTER),
			array(Events::FLUSH_BEFORE),
			array(Events::FLUSH_AFTER),
			array(Events::CLEAN_BEFORE),
			array(Events::CLEAN_AFTER),
		);
	}

	/**
	 * @dataProvider dataProviderCallbacks
	 */
	public function testCallbacks($cb, $expect)
	{
		$this->e->addCallbackListener(Events::LOAD_BEFORE, $cb);

		$listeners = $this->readAttribute($this->e, 'listeners');
		$this->assertSame(array(true, $expect), $listeners[Events::LOAD_BEFORE][0]);
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
			'invoke' => array($o = new Nette\Latte\Engine, array($o, '__invoke')),
			'static' => array('Events_addCallbackListener_Test::cb', array('Events_addCallbackListener_Test', 'cb')),
		);
	}

	public function testMore()
	{
		$listenersInit = $this->readAttribute($this->e, 'listeners');

		$cb = array($this, 'testAll');
		$this->e->addCallbackListener(Events::LOAD_BEFORE | Events::ATTACH | Events::REMOVE_AFTER, $cb);

		$listenersUnset = $listeners = $this->readAttribute($this->e, 'listeners');
		unset($listenersUnset[Events::LOAD_BEFORE][0]);
		unset($listenersUnset[Events::ATTACH][0]);
		unset($listenersUnset[Events::REMOVE_AFTER][0]);
		$this->assertSame($listenersInit, $listenersUnset);

		$this->assertSame(array(true, $cb), $listeners[Events::LOAD_BEFORE][0]);
		$this->assertSame(array(true, $cb), $listeners[Events::ATTACH][0]);
		$this->assertSame(array(true, $cb), $listeners[Events::REMOVE_AFTER][0]);
	}

	public function testInvalidCallback()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', 'Invalid callback.');
		$this->e->addCallbackListener(0, array());
	}

}
