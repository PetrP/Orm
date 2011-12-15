<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\OneToMany::add
 * @covers Orm\OneToMany::remove
 * @covers Orm\ValueEntityFragment::setValueHelper
 */
class Association_ManyToOne_and_OneToMany_NotNull_Test extends TestCase
{
	private $r;
	/** @var Association_Entity */
	private $e1;
	/** @var Association_Entity */
	private $e2;
	/** @var Association_Entity */
	private $e3;
	/** @var Association_Entity */
	private $e4;
	/** @var Association_Entity */
	private $e5;

	protected function setUp()
	{
		$orm = new RepositoryContainer;
		$this->r = $orm->{'Association_Repository'};
		foreach ($this->r->mapper->findAll() as $e)
		{
			$this->{'e' . $e->id} = $e;
		}
	}

	public function test1()
	{
		$this->e1->oneToManyNotNull->add($this->e1);
		$this->e1->oneToManyNotNull->add($this->e2);
		$this->e1->oneToManyNotNull->add($this->e3);
		$this->e1->oneToManyNotNull->add($this->e4);
		$this->e1->oneToManyNotNull->add($this->e5);
		$this->assertSame(array(1,2,3,4,5), $this->e1->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e1->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e2->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e3->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e4->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e5->manyToOneNotNull);

		$this->assertFalse(property_exists($this->e1, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e2, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e3, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e4, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e5, 'manyToOneNotNull'));
	}

	public function test2()
	{
		$this->e1->manyToOneNotNull = $this->e1;
		$this->e2->manyToOneNotNull = $this->e1;
		$this->e3->manyToOneNotNull = $this->e1;
		$this->e4->manyToOneNotNull = $this->e1;
		$this->e5->manyToOneNotNull = $this->e1;
		$this->assertSame(array(1,2,3,4,5), $this->e1->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e1->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e2->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e3->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e4->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e5->manyToOneNotNull);

		$this->assertFalse(property_exists($this->e1, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e2, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e3, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e4, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e5, 'manyToOneNotNull'));
	}

	public function test3()
	{
		$this->e1->manyToOneNotNull = $this->e1;
		$this->e2->manyToOneNotNull = $this->e1;
		$this->e1->oneToManyNotNull->add($this->e3);
		$this->e1->oneToManyNotNull->add($this->e4);
		$this->e5->manyToOneNotNull = $this->e1;

		$this->assertSame(array(1,2,3,4,5), $this->e1->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e1->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e2->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e3->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e4->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e5->manyToOneNotNull);

		$this->e1->oneToManyNotNull->remove($this->e2);
		$this->e1->oneToManyNotNull->remove($this->e3);
		$this->e1->oneToManyNotNull->remove($this->e4);
		$this->e1->oneToManyNotNull->remove($this->e5);

		$this->assertSame(array(1), $this->e1->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e2->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e3->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e4->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e5->manyToOneNotNull);

		$this->assertFalse(property_exists($this->e1, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e2, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e3, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e4, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e5, 'manyToOneNotNull'));
	}

	public function test4()
	{
		$this->e1->manyToOneNotNull = $this->e1;
		$this->e2->manyToOneNotNull = $this->e1;
		$this->e1->oneToManyNotNull->add($this->e3);
		$this->e1->oneToManyNotNull->add($this->e4);
		$this->e5->manyToOneNotNull = $this->e1;

		$this->assertSame(array(1,2,3,4,5), $this->e1->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e1->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e2->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e3->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e4->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e5->manyToOneNotNull);

		$this->e3->manyToOneNotNull = $this->e2;
		$this->e4->manyToOneNotNull = $this->e2;

		$this->assertSame(array(1,2,5), $this->e1->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(3,4), $this->e2->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e1->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e2->manyToOneNotNull);
		$this->assertSame($this->e2, $this->e3->manyToOneNotNull);
		$this->assertSame($this->e2, $this->e4->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e5->manyToOneNotNull);

		$this->assertFalse(property_exists($this->e1, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e2, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e3, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e4, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e5, 'manyToOneNotNull'));
	}

	public function test5()
	{
		$this->e1->fireEvent('onLoad', $this->r, array('id' => 1, 'manyToOneNotNull' => 1));
		$this->e2->fireEvent('onLoad', $this->r, array('id' => 2, 'manyToOneNotNull' => 1));
		$this->e3->fireEvent('onLoad', $this->r, array('id' => 3, 'manyToOneNotNull' => 1));
		$this->e4->fireEvent('onLoad', $this->r, array('id' => 4, 'manyToOneNotNull' => 1));
		$this->e5->fireEvent('onLoad', $this->r, array('id' => 5, 'manyToOneNotNull' => 1));

		$this->e3->manyToOneNotNull = 2;
		$this->e4->manyToOneNotNull = 2;

		$this->assertSame(array(1,2,5), $this->e1->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(3,4), $this->e2->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e1->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e2->manyToOneNotNull);
		$this->assertSame($this->e2, $this->e3->manyToOneNotNull);
		$this->assertSame($this->e2, $this->e4->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e5->manyToOneNotNull);

		$this->assertFalse(property_exists($this->e1, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e2, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e3, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e4, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e5, 'manyToOneNotNull'));
	}

	public function test6()
	{
		$this->e1->manyToOneNotNull = $this->e1;
		$this->e2->manyToOneNotNull = $this->e1;
		$this->e3->manyToOneNotNull = $this->e1;
		$this->e4->manyToOneNotNull = $this->e1;
		$this->e5->manyToOneNotNull = $this->e1;

		$this->assertSame(array(1,2,3,4,5), $this->e1->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e2->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e1->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e2->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e3->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e4->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e5->manyToOneNotNull);

		$this->e1->oneToManyNotNull->remove($this->e3);

		$this->assertSame(array(1,2,4,5), $this->e1->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e2->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e1->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e2->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e3->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e4->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e5->manyToOneNotNull);

		$this->e2->oneToManyNotNull->add($this->e3);

		$this->assertSame(array(1,2,4,5), $this->e1->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(3), $this->e2->oneToManyNotNull->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e1->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e2->manyToOneNotNull);
		$this->assertSame($this->e2, $this->e3->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e4->manyToOneNotNull);
		$this->assertSame($this->e1, $this->e5->manyToOneNotNull);

		$this->assertFalse(property_exists($this->e1, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e2, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e3, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e4, 'manyToOneNotNull'));
		$this->assertFalse(property_exists($this->e5, 'manyToOneNotNull'));
	}

}
