<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\OneToMany::add
 * @covers Orm\OneToMany::remove
 * @covers Orm\ValueEntityFragment::setValueHelper
 */
class Association_ManyToOne_and_OneToMany_Test extends TestCase
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
		$this->e1->oneToMany->add($this->e2);
		$this->e1->oneToMany->add($this->e3);
		$this->e1->oneToMany->add($this->e4);
		$this->e1->oneToMany->add($this->e5);
		$this->assertSame(array(2,3,4,5), $this->e1->oneToMany->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e2->manyToOne);
		$this->assertSame($this->e1, $this->e3->manyToOne);
		$this->assertSame($this->e1, $this->e4->manyToOne);
		$this->assertSame($this->e1, $this->e5->manyToOne);

		$this->assertFalse(property_exists($this->e1, 'manyToOne'));
		$this->assertFalse(property_exists($this->e2, 'manyToOne'));
		$this->assertFalse(property_exists($this->e3, 'manyToOne'));
		$this->assertFalse(property_exists($this->e4, 'manyToOne'));
		$this->assertFalse(property_exists($this->e5, 'manyToOne'));
	}

	public function test2()
	{
		$this->e2->manyToOne = $this->e1;
		$this->e3->manyToOne = $this->e1;
		$this->e4->manyToOne = $this->e1;
		$this->e5->manyToOne = $this->e1;
		$this->assertSame(array(2,3,4,5), $this->e1->oneToMany->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e2->manyToOne);
		$this->assertSame($this->e1, $this->e3->manyToOne);
		$this->assertSame($this->e1, $this->e4->manyToOne);
		$this->assertSame($this->e1, $this->e5->manyToOne);

		$this->assertFalse(property_exists($this->e1, 'manyToOne'));
		$this->assertFalse(property_exists($this->e2, 'manyToOne'));
		$this->assertFalse(property_exists($this->e3, 'manyToOne'));
		$this->assertFalse(property_exists($this->e4, 'manyToOne'));
		$this->assertFalse(property_exists($this->e5, 'manyToOne'));
	}

	public function test3()
	{
		$this->e2->manyToOne = $this->e1;
		$this->e1->oneToMany->add($this->e3);
		$this->e1->oneToMany->add($this->e4);
		$this->e5->manyToOne = $this->e1;

		$this->assertSame(array(2,3,4,5), $this->e1->oneToMany->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e2->manyToOne);
		$this->assertSame($this->e1, $this->e3->manyToOne);
		$this->assertSame($this->e1, $this->e4->manyToOne);
		$this->assertSame($this->e1, $this->e5->manyToOne);

		$this->e1->oneToMany->remove($this->e2);
		$this->e3->manyToOne = NULL;
		$this->e4->manyToOne = NULL;
		$this->e1->oneToMany->remove($this->e5);

		$this->assertSame(array(), $this->e1->oneToMany->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(NULL, $this->e2->manyToOne);
		$this->assertSame(NULL, $this->e3->manyToOne);
		$this->assertSame(NULL, $this->e4->manyToOne);
		$this->assertSame(NULL, $this->e5->manyToOne);

		$this->assertFalse(property_exists($this->e1, 'manyToOne'));
		$this->assertFalse(property_exists($this->e2, 'manyToOne'));
		$this->assertFalse(property_exists($this->e3, 'manyToOne'));
		$this->assertFalse(property_exists($this->e4, 'manyToOne'));
		$this->assertFalse(property_exists($this->e5, 'manyToOne'));
	}

	public function test4()
	{
		$this->e2->manyToOne = $this->e1;
		$this->e1->oneToMany->add($this->e3);
		$this->e1->oneToMany->add($this->e4);
		$this->e5->manyToOne = $this->e1;

		$this->assertSame(array(2,3,4,5), $this->e1->oneToMany->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e2->manyToOne);
		$this->assertSame($this->e1, $this->e3->manyToOne);
		$this->assertSame($this->e1, $this->e4->manyToOne);
		$this->assertSame($this->e1, $this->e5->manyToOne);

		$this->e3->manyToOne = $this->e2;
		$this->e4->manyToOne = $this->e2;

		$this->assertSame(array(2,5), $this->e1->oneToMany->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(3,4), $this->e2->oneToMany->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e2->manyToOne);
		$this->assertSame($this->e2, $this->e3->manyToOne);
		$this->assertSame($this->e2, $this->e4->manyToOne);
		$this->assertSame($this->e1, $this->e5->manyToOne);

		$this->assertFalse(property_exists($this->e1, 'manyToOne'));
		$this->assertFalse(property_exists($this->e2, 'manyToOne'));
		$this->assertFalse(property_exists($this->e3, 'manyToOne'));
		$this->assertFalse(property_exists($this->e4, 'manyToOne'));
		$this->assertFalse(property_exists($this->e5, 'manyToOne'));
	}

	public function test5()
	{
		$this->e2->fireEvent('onLoad', $this->r, array('id' => 2, 'manyToOne' => 1));
		$this->e3->fireEvent('onLoad', $this->r, array('id' => 3, 'manyToOne' => 1));
		$this->e4->fireEvent('onLoad', $this->r, array('id' => 4, 'manyToOne' => 1));
		$this->e5->fireEvent('onLoad', $this->r, array('id' => 5, 'manyToOne' => 1));

		$this->e3->manyToOne = 2;
		$this->e4->manyToOne = 2;

		$this->assertSame(array(2,5), $this->e1->oneToMany->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(3,4), $this->e2->oneToMany->get()->fetchPairs(NULL, 'id'));
		$this->assertSame($this->e1, $this->e2->manyToOne);
		$this->assertSame($this->e2, $this->e3->manyToOne);
		$this->assertSame($this->e2, $this->e4->manyToOne);
		$this->assertSame($this->e1, $this->e5->manyToOne);

		$this->assertFalse(property_exists($this->e1, 'manyToOne'));
		$this->assertFalse(property_exists($this->e2, 'manyToOne'));
		$this->assertFalse(property_exists($this->e3, 'manyToOne'));
		$this->assertFalse(property_exists($this->e4, 'manyToOne'));
		$this->assertFalse(property_exists($this->e5, 'manyToOne'));
	}

}
