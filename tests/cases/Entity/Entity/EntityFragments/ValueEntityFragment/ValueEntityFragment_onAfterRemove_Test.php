<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::onAfterRemove
 */
class ValueEntityFragment_onAfterRemove_Test extends TestCase
{
	private $r;
	private $e;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentityrepository;
		$this->e = $m->testentityrepository->getById(1);
	}

	public function test()
	{
		$e = $this->e;

		$this->assertSame(1, $e->id);
		$this->assertSame('string', $e->string);
		$this->assertSame('TestEntityRepository', get_class($e->repository));
		$this->assertSame(false, $e->isChanged());

		$this->r->remove($e);

		$this->assertSame(NULL, isset($e->id) ? $e->id : NULL);
		$this->assertSame('string', $e->string);
		$this->assertSame(NULL, $e->getRepository(false));
		$this->assertSame(true, $e->isChanged());
	}

	public function testRemoveAllRelationship()
	{
		$r = $this->r->model->getRepository('Association_Repository');
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
		//$e->oneToManyNotNull->add(...); // neni mozne priradit

		$e->manyToMany1->add(1);
		$e->manyToMany1->add(2);
		$e->manyToMany2->add(1);
		$e->manyToMany2->add(2);
		$e->manyToManySame->add(1);
		$e->manyToManySame->add(2);

		$r->flush();

		$this->assertSame(3, $e->id);

		$r->remove($e);

		$this->assertSame(NULL, $e->oneToOne1);
		$this->assertSame(NULL, $e->oneToOne2);
		$this->assertSame(NULL, $e->oneToOneSame);
		$this->assertSame(NULL, $e->oneToOne1NotNull);
		$this->assertSame(false, isset($e->oneToOne2NotNull));

		$this->assertSame(NULL, $e->manyToOne);
		$this->assertSame(false, isset($e->manyToOneNotNull));

		$this->assertSame(0, $e->oneToMany->count());
		$this->assertSame(0, $e->oneToManyNotNull->count());

		$this->assertSame(0, $e->manyToMany1->count());
		$this->assertSame(0, $e->manyToMany2->count());
		$this->assertSame(0, $e->manyToManySame->count());


		/** @var Association_Entity */
		$e1 = $r->getById(1);

		$this->assertSame(NULL, $e1->oneToOne2);
		$this->assertSame(NULL, $e1->oneToOne1);
		$this->assertSame(NULL, $e1->oneToOneSame);
		$this->assertSame(false, isset($e1->oneToOne2NotNull));
		$this->assertSame(NULL, $e1->oneToOne1NotNull);

		$this->assertSame(0, $e1->oneToMany->count());
		$this->assertSame(2, $e1->oneToManyNotNull->count());
		$this->assertSame(array($e1, $r->getById(2)), $e1->oneToManyNotNull->get()->fetchAll());

		$this->assertSame(NULL, $e1->manyToOne);
		$this->assertSame($e1, $e1->manyToOneNotNull);

		$this->assertSame(0, $e1->manyToMany2->count());
		$this->assertSame(0, $e1->manyToMany1->count());
		$this->assertSame(0, $e1->manyToManySame->count());

		$r->getById(1)->oneToOne2NotNull = 1;

		$r->flush();

		$this->assertSame(false, isset($e->id));
		$this->assertSame(NULL, $e->getRepository(false));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', 'onAfterRemove');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
