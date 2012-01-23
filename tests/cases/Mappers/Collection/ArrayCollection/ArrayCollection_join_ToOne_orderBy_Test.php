<?php

use Orm\ArrayCollection;
use Orm\RepositoryContainer;

/**
 * @covers Orm\ArrayCollection::orderBy
 * @covers Orm\ArrayCollection::_sort
 */
class ArrayCollection_join_ToOne_orderBy_Test extends TestCase
{
	/** @var ArrayCollection_join1_Repository */
	private $r1;
	/** @var ArrayCollection_join2_Repository */
	private $r2;
	/** @var ArrayCollection */
	private $c;
	/** @var array (id => IEntity) */
	private $d;
	private $d2;
	private $d3;
	private $d4;

	private function a($expectedIds, ArrayCollection $c)
	{
		$this->assertSame($expectedIds, $c->fetchPairs(NULL, 'id'));
	}

	protected function setUp()
	{
		$model = new RepositoryContainer;
		$this->r1 = $model->ArrayCollection_join1_;
		$this->r2 = $model->ArrayCollection_join2_;
		$this->c = $this->r1->mapper->findAll();
		$this->d = $this->c->fetchAssoc('id');
		$this->d2 = $this->r2->mapper->findAll()->fetchAssoc('id');

		$this->d[1]->join2 = $this->d2[5];
		$this->d[2]->join2 = $this->d2[3];
		$this->d[3]->join2 = $this->d2[2];
		$this->d[4]->join2 = $this->d2[4];
		$this->d[5]->join2 = $this->d2[1];

		$this->d2[1]->join1 = $this->d[3];
		$this->d2[2]->join1 = $this->d[2];
		$this->d2[3]->join1 = $this->d[1];
		$this->d2[4]->join1 = $this->d[5];
		$this->d2[5]->join1 = $this->d[4];

		$this->d3 = $model->ArrayCollection_join3_->mapper->findAll()->fetchAssoc('id');
		$this->d[1]->join3 = $this->d3[4];
		$this->d[2]->join3 = $this->d3[4];
		$this->d[3]->join3 = $this->d3[3];
		$this->d[4]->join3 = $this->d3[3];
		$this->d[5]->join3 = $this->d3[5];

		$this->d3[3]->join1 = $this->d[1];
		$this->d3[4]->join1 = $this->d[3];
		$this->d3[5]->join1 = $this->d[5];

		$this->d4 = $model->ArrayCollection_join4_->mapper->findAll()->fetchAssoc('id');
		$this->d[1]->join4 = $this->d4[5];
		$this->d[2]->join4 = $this->d4[2];
		$this->d[3]->join4 = $this->d4[5];
		$this->d[4]->join4 = $this->d4[2];
		$this->d[5]->join4 = $this->d4[5];
	}

	public function testNoJoin()
	{
		$this->a(array(1, 2, 3, 4, 5), $this->c);
	}

	public function testOneTable()
	{
		$this->a(array(5, 3, 2, 4, 1), $this->c->orderBy('join2->name'));
	}

	public function testOverTwoTable()
	{
		$this->a(array(2, 3, 5, 1, 4), $this->c->orderBy('join2->join1->name'));
	}

	public function testTwoJoin()
	{
		$this->d2[1]->join1 = $this->d[3];
		$this->d2[4]->join1 = $this->d[3];
		$this->d2[5]->join1 = $this->d[3];
		$this->a(array(2, 3, 5, 4, 1), $this->c->orderBy('join2->join1->name')->orderBy('join2->name'));
	}

	public function testFindBy()
	{
		$this->d2[1]->join1 = $this->d[3];
		$this->d2[4]->join1 = $this->d[3];
		$this->d2[5]->join1 = $this->d[3];
		$this->a(array(5, 4, 1), $this->c->{'findByJoin2->join1->name'}('c')->orderBy('join2->name'));
	}

	public function testFindBy2()
	{
		$this->d2[1]->join1 = $this->d[3];
		$this->d2[4]->join1 = $this->d[3];
		$this->d2[5]->join1 = $this->d[3];
		$this->a(array(5, 4, 1), $this->c->orderBy('join2->name')->findBy(array('join2->join1->name' => 'c')));
	}

	public function testEmpty()
	{
		// 1, 2, 3, 4, 5 neni protoze sortovani neni stabilni
		$this->a(array(4, 2, 1, 3, 5), $this->c->orderBy('joinNull->name'));
	}

	public function testUnexistFK()
	{
		$this->setExpectedException('Orm\MapperJoinException', 'ArrayCollection_join1_Repository: neni zadna vazba na `neexistuje`');
		$this->c->orderBy('neexistuje->name');
		$this->setExpectedException('Orm\InvalidArgumentException', "'neexistuje' is not key in 'neexistuje->name'");
		$this->c->getResult();
	}

	public function testUnexistFK2()
	{
		$this->setExpectedException('Orm\MapperJoinException', 'ArrayCollection_join2_Repository: neni zadna vazba na `neexistuje`');
		$this->c->orderBy('join2->neexistuje->name');
		$this->setExpectedException('Orm\InvalidArgumentException', "'neexistuje' is not key in 'join2->neexistuje->name'");
		$this->c->getResult();
	}

	public function testModifyFindAll()
	{
		$this->a(array(3, 4, 1, 2, 5), $this->c->orderBy('join3->name'));
	}

	public function testModifyFindAllOverMore()
	{
		$this->a(array(1, 5, 2, 3, 4), $this->c->orderBy('join2->join1->join3->join1->name'));
	}

	public function testFindAllWithJoin()
	{
		$this->a(array(2, 4, 1, 3, 5), $this->c->orderBy('join4->name'));
	}

}
