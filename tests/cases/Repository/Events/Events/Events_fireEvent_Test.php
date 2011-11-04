<?php

use Orm\Events;
use Orm\EventArguments;
use Orm\RepositoryContainer;
use Orm\Callback;

/**
 * @covers Orm\Events::fireEvent
 * @covers Orm\Events::handleLazy
 */
class Events_fireEvent_Test extends TestCase
{
	private $e;
	private $r;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->tests;
		$this->e = new Events($this->r);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Events', 'fireEvent');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function testReturn()
	{
		$return = $this->e->fireEvent(Events::ATTACH, new TestEntity);
		$this->assertSame($this->e, $return);
	}

	public function testInvalidEvent()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\Events::fireEvent() \$type must be valid event type; '2052' given.");
		$this->e->fireEvent(Events::REMOVE_AFTER | Events::ATTACH);
	}

	public function testNoEvent()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $e->getRepository(false));
		$this->e->fireEvent(Events::ATTACH, $e);
		$this->assertSame($this->r, $e->getRepository(false));
	}

	public function testNoEven_More_id()
	{
		$e = new TestEntity;
		$this->assertSame(false, isset($e->id));
		$args = array('id' => 123);
		$this->e->fireEvent(Events::PERSIST, $e, $args);
		$this->assertSame(true, isset($e->id));
		$this->assertSame(123, $e->id);
		$this->assertSame(array('id' => 123), $args);
	}

	public function testNoEven_More_data()
	{
		$e = new TestEntity;
		$this->assertSame('', $e->string);
		$args = array('data' => array('id' => 123, 'string' => 'foo'));
		$this->e->fireEvent(Events::HYDRATE_BEFORE, $e, $args);
		$this->assertSame(123, $e->id);
		$this->assertSame('foo', $e->string);
		$this->assertSame(array('data' => array('id' => 123, 'string' => 'foo')), $args);
	}

	public function testNoEvent_NoEntity()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$entity must be instance of Orm\\IEntity; 'NULL' given.");
		$this->e->fireEvent(Events::ATTACH);
	}

	public function testNoEven_More_noid()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$id must be scalar; 'NULL' given.");
		$this->e->fireEvent(Events::PERSIST, new TestEntity);
	}

	public function testNoEven_More_nodata()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$data must be array; 'NULL' given.");
		$this->e->fireEvent(Events::HYDRATE_BEFORE, new TestEntity);
	}

	/**
	 * @dataProvider dataProvideCallbacks
	 */
	public function testCallback($cb)
	{
		$this->e->addCallbackListener(Events::HYDRATE_BEFORE, $cb);
		$e = new TestEntity;
		$args = array('data' => array('id' => 123, 'string' => 'foo'));
		$this->e->fireEvent(Events::HYDRATE_BEFORE, $e, $args);
		$this->assertSame('foo muhaha', $e->string);
		$this->assertSame(array('data' => array('id' => 123, 'string' => 'foo muhaha')), $args);
	}

	public function dataProvideCallbacks()
	{
		return array(
			'closure' => array(function (EventArguments $args) {
				$args->data['string'] .= ' muhaha';
			}),
			'create_function' => array(create_function('Orm\EventArguments $args', '
				$args->data["string"] .= " muhaha";
			')),
			'array' => array(array($this, 'cb')),
			'array2' => array(array('Events_fireEvent_Test', 'cb')),
			'static' => array('Events_fireEvent_Test::cb'),
			'Nette' => array(callback($this, 'cb')),
			'Orm' => array(Callback::create($this, 'cb')),
		);
	}

	public static function cb(EventArguments $args)
	{
		$args->data['string'] .= ' muhaha';
	}

	public function testCallbackMore()
	{
		$this->e->addCallbackListener(Events::HYDRATE_BEFORE, array($this, 'cb'));
		$this->e->addCallbackListener(Events::HYDRATE_BEFORE, array($this, 'cb'));
		$e = new TestEntity;
		$args = array('data' => array('id' => 123, 'string' => 'foo'));
		$this->e->fireEvent(Events::HYDRATE_BEFORE, $e, $args);
		$this->assertSame('foo muhaha muhaha', $e->string);
		$this->assertSame(array('data' => array('id' => 123, 'string' => 'foo muhaha muhaha')), $args);
	}

	public function testCallbackCheck()
	{
		$this->e->addCallbackListener(Events::HYDRATE_BEFORE, function (EventArguments $args) {
			$args->data = NULL;
		});
		$this->e->addCallbackListener(Events::HYDRATE_BEFORE, array($this, 'cb'));

		$args = array('data' => array('id' => 123, 'string' => 'foo'));
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$data must be array; 'NULL' given.");
		$this->e->fireEvent(Events::HYDRATE_BEFORE, new TestEntity, $args);
		$this->assertSame(array('data' => array('id' => 123, 'string' => 'foo')), $args);
	}

	public function testLazyOnce()
	{
		$count = (object) array('count' => 0, 'e' => NULL);
		$this->e->addLazyListener(Events::HYDRATE_BEFORE, function () use ($count) {
			$count->count++;
			return $count->e = new Events_addListener_Hydrate_before;
		});
		$e = new TestEntity;
		$this->assertSame(0, $count->count);
		$this->assertSame(NULL, $count->e);
		$args = array('data' => array('id' => 123, 'string' => 'foo'));
		$this->e->fireEvent(Events::HYDRATE_BEFORE, $e, $args);
		$this->assertSame(array('data' => array('id' => 123, 'string' => 'foo')), $args);
		$this->assertSame(1, $count->count);
		$this->assertInstanceOf('Events_addListener_Hydrate_before', $count->e);
		$this->assertSame(1, $count->e->count);
		$args = array('data' => array('id' => 123, 'string' => 'foo'));
		$this->e->fireEvent(Events::HYDRATE_BEFORE, $e, $args);
		$this->assertSame(array('data' => array('id' => 123, 'string' => 'foo')), $args);
		$this->assertSame(1, $count->count);
		$this->assertSame(2, $count->e->count);
	}

	/**
	 * @dataProvider dataProvideLazyFactories
	 */
	public function testLazy($cb)
	{
		Events_addListener_Base::$logs = array();
		$this->e->addLazyListener(Events::HYDRATE_BEFORE, $cb);
		$e = new TestEntity;

		$args = array('data' => array('id' => 123, 'string' => 'foo'));
		$this->e->fireEvent(Events::HYDRATE_BEFORE, $e, $args);
		$this->assertSame(array('data' => array('id' => 123, 'string' => 'foo')), $args);

		$this->assertSame(1, count(Events_addListener_Base::$logs));
		$this->assertInstanceOf('Events_addListener_Hydrate_before', Events_addListener_Base::$logs[0][0]);
		$this->assertSame('onBeforeHydrateEvent', Events_addListener_Base::$logs[0][1]);
		$this->assertInstanceOf('Orm\EventArguments', Events_addListener_Base::$logs[0][2]);

		$this->assertSame(Events::HYDRATE_BEFORE, Events_addListener_Base::$logs[0][2]->type);
		$this->assertSame($this->r, Events_addListener_Base::$logs[0][2]->repository);
		$this->assertSame($e, Events_addListener_Base::$logs[0][2]->entity);
		$this->assertSame(array('id' => 123, 'string' => 'foo'), Events_addListener_Base::$logs[0][2]->data);

		$this->assertAttributeSame(array(), 'lazy', $this->e);
	}

	public function dataProvideLazyFactories()
	{
		return array(
			'closure' => array(function () {
				return new Events_addListener_Hydrate_before;
			}),
			'create_function' => array(create_function('', '
				return new Events_addListener_Hydrate_before;
			')),
			'array' => array(array($this, 'cbf')),
			'array2' => array(array('Events_fireEvent_Test', 'cbf')),
			'static' => array('Events_fireEvent_Test::cbf'),
			'Nette' => array(callback($this, 'cbf')),
			'Orm' => array(Callback::create($this, 'cbf')),
		);
	}

	public static function cbf()
	{
		return new Events_addListener_Hydrate_before;
	}

	public function testLazyInvalid()
	{
		$this->e->addLazyListener(Events::ATTACH, $f = function () {
			return (object) array();
		});
		$f = Callback::create($f)->__toString();
		$this->assertContains($f, array('{closure}', '{lambda}'));
		$this->setExpectedException('Orm\BadReturnException', "Orm\\Events lazy factory $f() must return Orm\\IListener, 'stdClass' given.");
		$this->e->fireEvent(Events::ATTACH, new TestEntity);
	}

	public function testLazyInvalid2()
	{
		$this->e->addLazyListener(Events::ATTACH, array($this, 'dataProvideLazyFactories'));
		$this->setExpectedException('Orm\BadReturnException', "Orm\\Events lazy factory Events_fireEvent_Test::dataProvideLazyFactories() must return Orm\\IListener, 'array' given.");
		$this->e->fireEvent(Events::ATTACH, new TestEntity);
	}

	public function testLazyInvalidType()
	{
		$this->e->addLazyListener(Events::ATTACH, $f = function () {
			return new Events_addListener_Persist;
		});
		$f = Callback::create($f)->__toString();
		$this->assertContains($f, array('{closure}', '{lambda}'));
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\Events lazy factory $f() must return Orm\\IListenerAttach; 'Events_addListener_Persist' given.");
		$this->e->fireEvent(Events::ATTACH, new TestEntity);
	}

	public function testLazyExtraType()
	{
		$this->e->addLazyListener(Events::REMOVE_BEFORE, $f = function () {
			return new Events_addListener_Remove;
		});
		$f = Callback::create($f)->__toString();
		$this->assertContains($f, array('{closure}', '{lambda}'));
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\Events lazy factory $f() returns not expected Orm\\IListenerRemoveAfter; 'Events_addListener_Remove'.");
		$this->e->fireEvent(Events::REMOVE_BEFORE, new TestEntity);
	}

	public function testLazyMore()
	{
		Events_addListener_Base::$logs = array();
		$count = (object) array('count' => 0, 'e' => NULL);
		$this->e->addLazyListener(Events::REMOVE_BEFORE | Events::REMOVE_AFTER, function () use ($count) {
			$count->count++;
			return $count->e = new Events_addListener_Remove;
		});
		$e = new TestEntity;
		$this->assertSame(0, $count->count);
		$this->assertSame(NULL, $count->e);
		$this->e->fireEvent(Events::REMOVE_BEFORE, $e);
		$this->assertSame(1, $count->count);
		$this->assertInstanceOf('Events_addListener_Remove', $count->e);
		$this->assertSame(1, $count->e->count);
		$this->e->fireEvent(Events::REMOVE_AFTER, $e);
		$this->assertSame(1, $count->count);
		$this->assertSame(2, $count->e->count);

		$this->assertSame(2, count(Events_addListener_Base::$logs));

		$log = Events_addListener_Base::$logs[0];
		$this->assertInstanceOf('Events_addListener_Remove', $log[0]);
		$this->assertSame('onBeforeRemoveEvent', $log[1]);
		$this->assertInstanceOf('Orm\EventArguments', $log[2]);
		$this->assertSame(Events::REMOVE_BEFORE, $log[2]->type);
		$this->assertSame($this->r, $log[2]->repository);
		$this->assertSame($e, $log[2]->entity);

		$log = Events_addListener_Base::$logs[1];
		$this->assertInstanceOf('Events_addListener_Remove', $log[0]);
		$this->assertSame('onAfterRemoveEvent', $log[1]);
		$this->assertInstanceOf('Orm\EventArguments', $log[2]);
		$this->assertSame(Events::REMOVE_AFTER, $log[2]->type);
		$this->assertSame($this->r, $log[2]->repository);
		$this->assertSame($e, $log[2]->entity);

		$this->assertSame(Events_addListener_Base::$logs[0][0], Events_addListener_Base::$logs[1][0]);
	}

	public function testArgumentsReset_NoEvent()
	{
		$e = new TestEntity;
		$args = array('id' => 123, 'data' => array('id' => 123, 'string' => 'foo'));
		$this->e->fireEvent(Events::REMOVE_BEFORE, $e, $args);
		$this->assertSame(array(), $args);
	}

	public function testArgumentsReset_HasEvent()
	{
		$this->e->addCallbackListener(Events::REMOVE_BEFORE, function () {});
		$e = new TestEntity;
		$args = array('id' => 123, 'data' => array('id' => 123, 'string' => 'foo'));
		$this->e->fireEvent(Events::REMOVE_BEFORE, $e, $args);
		$this->assertSame(array(), $args);
	}
}
