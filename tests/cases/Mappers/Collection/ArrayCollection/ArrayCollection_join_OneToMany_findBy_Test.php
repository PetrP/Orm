<?php

use Orm\ArrayCollection;
use Orm\RepositoryContainer;

/**
 * @covers Orm\ArrayCollection::findBy
 */
class ArrayCollection_join_OneToMany_findBy_Test extends TestCase
{
	/** @var ArrayCollection_join_OneToMany1_Repository */
	private $r1;
	/** @var ArrayCollection_join_OneToMany2_Repository */
	private $r2;
	/** @var ArrayCollection */
	private $c;
	/** @var array (id => IEntity) */
	private $d;
	private $d2;

	private function a($expectedIds, ArrayCollection $c)
	{
		$this->assertSame($expectedIds, $c->fetchPairs(NULL, 'id'));
	}

	protected function setUp()
	{
		$model = new RepositoryContainer;
		$this->r1 = $model->ArrayCollection_join_OneToMany1_;
		$this->r2 = $model->ArrayCollection_join_OneToMany2_;
		$this->c = $this->r1->mapper->findAll();
		$this->d = $this->c->fetchAssoc('id');
		$this->d2 = $this->r2->mapper->findAll()->fetchAssoc('id');

		$this->d[1]->join = $this->d2[5];
		$this->d[2]->join = $this->d2[3];
		$this->d[3]->join = $this->d2[2];
		$this->d[4]->join = $this->d2[1];
		$this->d[5]->join = $this->d2[4];

		$this->d2[1]->join = $this->d[3];
		$this->d2[2]->join = $this->d[2];
		$this->d2[3]->join = $this->d[1];
		$this->d2[4]->join = $this->d[5];
		$this->d2[5]->join = $this->d[4];

		/*
		d1   d2   d1
		1 => 3 => 2
		2 => 2 => 3
		3 => 1 => 4
		4 => 5 => 1
		5 => 4 => 5
		 */
	}

	public function testOneTable()
	{
		$this->a(array(1, 2, 3), $this->c->findBy(array('joins->name' => array('a', 'b', 'c'))));
	}

	public function testOverTwoTable1()
	{
		$this->a(array(1, 2, 5), $this->c->findBy(array('joins->join->name' => array('a', 'b', 'e'))));
	}

	public function testOverTwoTable2()
	{
		$this->a(array(1, 2, 4), $this->c->findBy(array('joins->joins->name' => array('a', 'b', 'c'))));
	}

	public function testTwoJoin()
	{
		$this->a(array(1, 4), $this->c->findBy(array('joins->joins->name' => array('a', 'b', 'c')))->findBy(array('joins->name' => array('c', 'd', 'e'))));
	}

	public function testFirst()
	{
		$this->d[1]->joins->remove(3);
		$this->d[2]->joins->remove(2);
		$this->d[3]->joins->remove(1);
		$this->d[4]->joins->remove(5);
		$this->d[5]->joins->remove(4);

		$this->d[1]->joins->add(2);
		$this->d[4]->joins->add(1);
		$this->d[4]->joins->add(3);
		$this->d[4]->joins->add(5);
		$this->d[5]->joins->add(4);

		$this->a(array(1, 4), $this->c->findBy(array('joins->name' => array('a', 'b', 'c'))));
	}

	public function testEmpty()
	{
		$this->a(array(), $this->c->findBy(array('joinsNull->name' => array('a', 'b', 'c'))));
	}

	public function testUnexist1()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "'neexistuje' is not key in 'neexistuje->name'");
		$this->c->findBy(array('joins->neexistuje->name' => array('a', 'b', 'c')));
	}

	public function testUnexist2()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "'neexistuje' is not key in 'neexistuje->name'");
		$this->c->findBy(array('neexistuje->name' => array('a', 'b', 'c')));
	}

}
