<?php

use Orm\Events;
use Orm\EventArguments;
use Orm\SqlConventional;

/**
 * @covers Orm\DibiPersistenceHelper::toArray
 */
class DibiPersistenceHelper_toArray_events_Test extends DibiPersistenceHelper_Test
{

	protected function setUp()
	{
		parent::setUp();
		$this->e->miXed3 = array('a', 'b');
	}

	private function t()
	{
		return $this->h->call('toArray', array($this->e, NULL, 'insert'));
	}

	public function testBefore()
	{
		$test = $this;
		$e = $this->e;
		$this->r->events->addCallbackListener(Events::SERIALIZE_BEFORE, function (EventArguments $args) use ($test, $e) {
			$test->assertSame($e, $args->entity);
			$test->assertSame(array(
				'id' => false,
				'miXed' => true,
				'miXed2' => true,
				'miXed3' => true,
			), $args->params);

			$test->assertSame(array(
				'id' => NULL,
				'miXed' => 1,
				'miXed2' => 2,
				'miXed3' => array('a', 'b'),
			), $args->values);

			$args->values['miXed2'] = 'foo bar';
			$args->values['aaa'] = 'bbb';
			$args->params['aaa'] = true;
			$args->values['bbb'] = 'aaa'; // trochu wtf chovany protoze ty ktere nejsou v params se ignoruji
			$test->assertSame('insert', $args->operation);
		});
		$this->assertSame(array(
			'mi_xed' => 1,
			'mi_xed2' => 'foo bar',
			'mi_xed3' => 'a:2:{i:0;s:1:"a";i:1;s:1:"b";}',
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
				'miXed' => 1,
				'miXed2' => 2,
				'miXed3' => 'a:2:{i:0;s:1:"a";i:1;s:1:"b";}',
			), $args->values);

			$args->values['miXed2'] = 'foo bar';
			$args->values['aaa'] = 'bbb';
			$test->assertSame('insert', $args->operation);
		});
		$this->assertSame(array(
			'mi_xed' => 1,
			'mi_xed2' => 'foo bar',
			'mi_xed3' => 'a:2:{i:0;s:1:"a";i:1;s:1:"b";}',
			'aaa' => 'bbb',
		), $this->t());
	}

	public function testConventional()
	{
		$test = $this;
		$e = $this->e;
		$this->r->events->addCallbackListener(Events::SERIALIZE_BEFORE, function (EventArguments $args) use ($test) {
			$test->assertSame(array(
				'id' => NULL,
				'miXed' => 1,
				'miXed2' => 2,
				'miXed3' => array('a', 'b'),
			), $args->values);
		});
		$this->r->events->addCallbackListener(Events::SERIALIZE_AFTER, function (EventArguments $args) use ($test, $e) {
			$test->assertSame(array(
				'miXed' => 1,
				'miXed2' => 2,
				'miXed3' => 'a:2:{i:0;s:1:"a";i:1;s:1:"b";}',
			), $args->values);
		});
		$this->r->events->addCallbackListener(Events::SERIALIZE_CONVENTIONAL, function (EventArguments $args) use ($test, $e) {
			$test->assertSame($e, $args->entity);
			$test->assertSame(array(
				'mi_xed' => 1,
				'mi_xed2' => 2,
				'mi_xed3' => 'a:2:{i:0;s:1:"a";i:1;s:1:"b";}',
			), $args->values);

			$args->values['mi_xed2'] = 'foo bar';
			$args->values['aaa'] = 'bbb';
			$test->assertSame('insert', $args->operation);
		});
		$this->assertSame(array(
			'mi_xed' => 1,
			'mi_xed2' => 'foo bar',
			'mi_xed3' => 'a:2:{i:0;s:1:"a";i:1;s:1:"b";}',
			'aaa' => 'bbb',
		), $this->t());
	}

	public function testAll()
	{
		$test = $this;
		$e = $this->e;
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
			'mi_xed' => 1111,
			'mi_xed2' => 2,
			'mi_xed3' => 'a:2:{i:0;s:1:"a";i:1;s:1:"b";}',
		), $this->t());
	}

	public function testInsert()
	{
		$test = $this;
		$e = $this->e;
		$x = (object) array('c' => 0);
		$this->r->events->addCallbackListener(Events::SERIALIZE_BEFORE, function (EventArguments $args) use ($test, $x) {
			$test->assertSame(NULL, $args->values['id']);
			$test->assertSame('insert', $args->operation);
			$x->c++;
		});
		$this->r->events->addCallbackListener(Events::SERIALIZE_AFTER, function (EventArguments $args) use ($test, $e, $x) {
			$test->assertSame(false, isset($args->values['id']));
			$test->assertSame('insert', $args->operation);
			$x->c++;
		});
		$this->r->events->addCallbackListener(Events::SERIALIZE_CONVENTIONAL, function (EventArguments $args) use ($test, $e, $x) {
			$test->assertSame(false, isset($args->values['id']));
			$test->assertSame('insert', $args->operation);
			$x->c++;
		});

		$this->d->addExpected('begin', NULL, NULL);
		$this->d->addExpected('query', true, "INSERT INTO `dibipersistencehelper_` (`mi_xed`, `mi_xed2`, `mi_xed3`) VALUES (1, 2, 'a:2:{i:0;s:1:\\\"a\\\";i:1;s:1:\\\"b\\\";}')");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$this->r->persist($this->e);
		$this->assertSame(3, $x->c);
	}

	public function testUpdate()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->d->addExpected('query', true, "INSERT INTO `dibipersistencehelper_` (`mi_xed`, `mi_xed2`, `mi_xed3`) VALUES (1, 2, 'a:2:{i:0;s:1:\\\"a\\\";i:1;s:1:\\\"b\\\";}')");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$this->r->persist($this->e);

		$this->e->markAsChanged();
		$test = $this;
		$e = $this->e;
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

		$this->d->addExpected('query', true, "SELECT `id` FROM `dibipersistencehelper_` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('id' => 3), true);
		$this->d->addExpected('query', true, "UPDATE `dibipersistencehelper_` SET `id`=3, `mi_xed`=1, `mi_xed2`=2, `mi_xed3`='a:2:{i:0;s:1:\\\"a\\\";i:1;s:1:\\\"b\\\";}' WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->r->persist($this->e);
		$this->assertSame(3, $x->c);
	}

}
