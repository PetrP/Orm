<?php

use Orm\RepositoryContainer;
use Orm\Events;
use Orm\EventArguments;
use Orm\SqlConventional;

/**
 * @covers Orm\ArrayMapper::flush
 */
class ArrayMapper_flush_pesist_events_Test extends TestCase
{
	private $r;
	private $m;
	private $e;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->ArrayMapper_flush_Repository;
		$this->m = $this->r->mapper;
		$this->e = new ArrayMapper_flush_Entity;
		$this->e->date = '2011-11-11';
	}

	private function t()
	{
		$this->r->remove(1);
		$this->r->remove(2);
		$this->m->persist($this->e);
		$this->m->flush();
		$storage = $this->readAttribute($this->m, 'array');
		$this->assertSame(3, count($storage));
		return $storage[3];
	}

	public function testBefore()
	{
		$test = $this;
		$e = $this->e;
		$this->r->events->addCallbackListener(Events::SERIALIZE_BEFORE, function (EventArguments $args) use ($test, $e) {
			$test->assertSame($e, $args->entity);
			$test->assertSame(array(), $args->params);

			$test->assertInstanceOf('DateTime', $args->values['date']);
			unset($args->values['date']);
			$test->assertSame(array(
				'id' => NULL,
				'string' => '',
				'mixed' => NULL,
				'miXed' => NULL,
			), $args->values);

			$args->values['mixed'] = 'foo bar';
			$args->values['aaa'] = 'bbb';
			$test->assertSame('insert', $args->operation);
		});
		$this->assertSame(array(
			'id' => NULL, // protoze se nepersistuje pres repository
			'string' => '',
			'mixed' => 'foo bar',
			'miXed' => NULL,
			'aaa' => 'bbb',
		), $this->t());
	}

	public function testAfter()
	{
		$test = $this;
		$e = $this->e;
		$this->r->events->addCallbackListener(Events::SERIALIZE_AFTER, function (EventArguments $args) use ($test, $e) {
			$test->assertSame($e, $args->entity);
			$test->assertSame(array(
				'id' => NULL,
				'string' => '',
				'date' => '2011-11-11T00:00:00+01:00',
				'mixed' => NULL,
				'miXed' => NULL,
			), $args->values);

			$args->values['mixed'] = 'foo bar';
			$args->values['aaa'] = 'bbb';
			$test->assertSame('insert', $args->operation);
		});
		$this->assertSame(array(
			'id' => NULL, // protoze se nepersistuje pres repository
			'string' => '',
			'date' => '2011-11-11T00:00:00+01:00',
			'mixed' => 'foo bar',
			'miXed' => NULL,
			'aaa' => 'bbb',
		), $this->t());
	}

	public function testConventional()
	{
		$this->m->conv = new SqlConventional($this->m);
		$test = $this;
		$e = $this->e;
		$this->r->events->addCallbackListener(Events::SERIALIZE_BEFORE, function (EventArguments $args) use ($test) {
			$d = $args->values['date'];
			$args->values['date'] = NULL;
			$test->assertSame(array(
				'id' => NULL,
				'string' => '',
				'date' => NULL,
				'mixed' => NULL,
				'miXed' => NULL,
			), $args->values);
			$args->values['date'] = $d;
		});
		$this->r->events->addCallbackListener(Events::SERIALIZE_AFTER, function (EventArguments $args) use ($test, $e) {
			$test->assertSame(array(
				'id' => NULL,
				'string' => '',
				'date' => '2011-11-11T00:00:00+01:00',
				'mixed' => NULL,
				'miXed' => NULL,
			), $args->values);
		});
		$this->r->events->addCallbackListener(Events::SERIALIZE_CONVENTIONAL, function (EventArguments $args) use ($test, $e) {
			$test->assertSame($e, $args->entity);
			$test->assertSame(array(
				'id' => NULL,
				'string' => '',
				'date' => '2011-11-11T00:00:00+01:00',
				'mixed' => NULL,
				'mi_xed' => NULL, // conventional
			), $args->values);

			$args->values['mixed'] = 'foo bar';
			$args->values['aaa'] = 'bbb';
			$test->assertSame('insert', $args->operation);
		});
		$this->assertSame(array(
			'id' => NULL, // protoze se nepersistuje pres repository
			'string' => '',
			'date' => '2011-11-11T00:00:00+01:00',
			'mixed' => 'foo bar',
			'mi_xed' => NULL,
			'aaa' => 'bbb',
		), $this->t());
	}

	public function testAll()
	{
		$this->m->conv = new SqlConventional($this->m);
		$test = $this;
		$e = $this->e;
		$this->e->miXed = 1;
		$this->r->events->addCallbackListener(Events::SERIALIZE_BEFORE, function (EventArguments $args) use ($test) {
			$test->assertSame(1, $args->values['miXed']);
			$args->values['miXed'] = 11;
		});
		$this->r->events->addCallbackListener(Events::SERIALIZE_AFTER, function (EventArguments $args) use ($test, $e) {
			$test->assertSame(11, $args->values['miXed']);
			$args->values['miXed'] = 111;
		});
		$this->r->events->addCallbackListener(Events::SERIALIZE_CONVENTIONAL, function (EventArguments $args) use ($test, $e) {
			$test->assertSame(111, $args->values['mi_xed']);
			$args->values['mi_xed'] = 1111;
		});
		$this->assertSame(array(
			'id' => NULL, // protoze se nepersistuje pres repository
			'string' => '',
			'date' => '2011-11-11T00:00:00+01:00',
			'mixed' => NULL,
			'mi_xed' => 1111,
		), $this->t());
	}

	public function testInsert()
	{
		$this->r->remove(1);
		$this->r->remove(2);
		$test = $this;
		$e = $this->e;
		$this->e->miXed = 1;
		$x = (object) array('c' => 0);
		$this->r->events->addCallbackListener(Events::SERIALIZE_BEFORE, function (EventArguments $args) use ($test, $x) {
			$test->assertSame(3, $args->values['id']);
			$test->assertSame('insert', $args->operation);
			$x->c++;
		});
		$this->r->events->addCallbackListener(Events::SERIALIZE_AFTER, function (EventArguments $args) use ($test, $e, $x) {
			$test->assertSame(3, $args->values['id']);
			$test->assertSame('insert', $args->operation);
			$x->c++;
		});
		$this->r->events->addCallbackListener(Events::SERIALIZE_CONVENTIONAL, function (EventArguments $args) use ($test, $e, $x) {
			$test->assertSame(3, $args->values['id']);
			$test->assertSame('insert', $args->operation);
			$x->c++;
		});
		$this->r->persistAndFlush($this->e);
		$this->assertSame(3, $x->c);
	}

	public function testUpdate()
	{
		$this->r->remove(1);
		$this->r->remove(2);
		$this->r->persistAndFlush($this->e); // aby poznalo ze je update musi byt flushnuto
		$test = $this;
		$e = $this->e;
		$this->e->miXed = 1;
		$x = (object) array('c' => 0);
		$this->r->events->addCallbackListener(Events::SERIALIZE_BEFORE, function (EventArguments $args) use ($test, $x) {
			$test->assertSame(3, $args->values['id']);
			$test->assertSame('update', $args->operation);
			$x->c++;
		});
		$this->r->events->addCallbackListener(Events::SERIALIZE_AFTER, function (EventArguments $args) use ($test, $e, $x) {
			$test->assertSame(3, $args->values['id']);
			$test->assertSame('update', $args->operation);
			$x->c++;
		});
		$this->r->events->addCallbackListener(Events::SERIALIZE_CONVENTIONAL, function (EventArguments $args) use ($test, $e, $x) {
			$test->assertSame(3, $args->values['id']);
			$test->assertSame('update', $args->operation);
			$x->c++;
		});
		$this->r->persistAndFlush($this->e);
		$this->assertSame(3, $x->c);
	}

	public function testParamsAreNotSupported()
	{
		$test = $this;
		$e = $this->e;
		$this->r->events->addCallbackListener(Events::SERIALIZE_BEFORE, function (EventArguments $args) use ($test, $e) {
			$test->assertSame(array(), $args->params);
			$args->params['foo'] = 'bar';
		});
		$this->setExpectedException('Orm\NotSupportedException', 'Orm\EventArguments::$params are not supported for Orm\ArrayMapper during Orm\Events::SERIALIZE_BEFORE event.');
		$this->t();
	}

}
