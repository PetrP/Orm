<?php

use Orm\RepositoryContainer;
use Orm\Events;


/**
 * @covers Orm\ValueEntityFragment::onBeforeRemove
 */
class ValueEntityFragment_onBeforeRemove_Test extends TestCase
{

	public function testAllRelationshipPrefetch()
	{
		$m = new RepositoryContainer;
		$r = $m->getRepository('Association_Repository');
		$r->mapper->array = array(
			1 => array('id' => 1, 'manyToOneNotNull' => 1, 'oneToOne2NotNull' => 1),
			2 => array('id' => 2, 'manyToOneNotNull' => 1, 'oneToOne2NotNull' => 1),
		);

		/** @var Association_Entity */
		$e = $r->attach(new Association_Entity);

		$e->oneToOne1 = 1;
		$e->oneToOne2 = 1;
		$e->oneToOneSame = 1;
		$e->oneToOne1NotNull = 1;
		$e->oneToOne2NotNull = 1;

		$e->manyToOne = 1;
		$e->manyToOneNotNull = 1;

		$e->oneToMany->add(1);
		$e->oneToMany->add(2);
		//$e->oneToManyNotNull->add(1); // neni mozne priradit, nastavi se primo ve storage nize

		$e->manyToMany1->add(1);
		$e->manyToMany1->add(2);
		$e->manyToMany2->add(1);
		$e->manyToMany2->add(2);
		$e->manyToManySame->add(1);
		$e->manyToManySame->add(2);

		$r->flush();

		$this->assertSame(3, $e->id);

		$storage = $r->mapper->array;
		$m = new RepositoryContainer;
		$r = $m->getRepository('Association_Repository');
		$storage[1]['manyToOneNotNull'] = 3;
		$r->mapper->array = $storage;
		$e = $r->getById(3);

		$test = $this;
		$wasEventRunned = false;
		$r->events->addCallbackListener(Events::REMOVE_AFTER, function ($args) use ($test, $r, $m, & $wasEventRunned) {
			$e = $args->entity;
			$test->assertSame(3, $e->id);
			$test->assertSame($r, $e->getRepository(false));
			$test->assertSame($m, $e->getModel(false));

			$test->assertSame(1, $e->oneToOne1->id);
			$test->assertSame(1, $e->oneToOne2->id);
			$test->assertSame(1, $e->oneToOneSame->id);
			$test->assertSame(1, $e->oneToOne1NotNull->id);
			$test->assertSame(1, $e->oneToOne2NotNull->id);

			$test->assertSame(1, $e->manyToOne->id);
			$test->assertSame(1, $e->manyToOneNotNull->id);

			$test->assertSame(2, $e->oneToMany->count());
			$test->assertSame(array(1, 2), $e->oneToMany->get()->fetchPairs(NULL, 'id'));
			$test->assertSame(1, $e->oneToManyNotNull->count());
			$test->assertSame(array(1), $e->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));

			$test->assertSame(2, $e->manyToMany1->count());
			$test->assertSame(array(1, 2), $e->manyToMany1->get()->fetchPairs(NULL, 'id'));
			$test->assertSame(2, $e->manyToMany2->count());
			$test->assertSame(array(1, 2), $e->manyToMany2->get()->fetchPairs(NULL, 'id'));
			$test->assertSame(2, $e->manyToManySame->count());
			$test->assertSame(array(1, 2), $e->manyToManySame->get()->fetchPairs(NULL, 'id'));

			$wasEventRunned = true;
		});

		$r->remove($e);

		$this->assertSame(true, $wasEventRunned);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', 'onBeforeRemove');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
