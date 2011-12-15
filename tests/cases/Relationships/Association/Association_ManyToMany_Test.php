<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ManyToMany::add
 * @covers Orm\ManyToMany::remove
 */
class Association_ManyToMany_Test extends TestCase
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
		$this->e1->manyToMany1->add($this->e2);
		$this->e1->manyToMany1->add($this->e3);
		$this->e1->manyToMany1->add($this->e4);
		$this->e1->manyToMany1->add($this->e5);
		$this->assertSame(array(2,3,4,5), $this->e1->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e2->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e3->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e4->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e5->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e1->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e2->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e3->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e4->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e5->manyToMany2->get()->fetchPairs(NULL, 'id'));
	}

	public function test2()
	{
		$this->e2->manyToMany2->add($this->e1);
		$this->e3->manyToMany2->add($this->e1);
		$this->e4->manyToMany2->add($this->e1);
		$this->e5->manyToMany2->add($this->e1);
		$this->assertSame(array(2,3,4,5), $this->e1->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e2->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e3->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e4->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e5->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e1->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e2->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e3->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e4->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e5->manyToMany2->get()->fetchPairs(NULL, 'id'));
	}

	public function test3()
	{
		$this->e2->manyToMany2->add($this->e1);
		$this->e1->manyToMany1->add($this->e3);
		$this->e1->manyToMany1->add($this->e4);
		$this->e5->manyToMany2->add($this->e1);

		$this->assertSame(array(2,3,4,5), $this->e1->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e2->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e3->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e4->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e5->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e1->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e2->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e3->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e4->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e5->manyToMany2->get()->fetchPairs(NULL, 'id'));

		$this->e2->manyToMany2->remove($this->e1);
		$this->e1->manyToMany1->remove($this->e3);
		$this->e1->manyToMany1->remove($this->e4);
		$this->e5->manyToMany2->remove($this->e1);

		$this->assertSame(array(), $this->e1->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e2->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e3->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e4->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e5->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e1->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e2->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e3->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e4->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e5->manyToMany2->get()->fetchPairs(NULL, 'id'));
	}

	public function test4()
	{
		$this->e2->manyToMany2->add($this->e1);
		$this->e1->manyToMany1->add($this->e3);
		$this->e1->manyToMany1->add($this->e4);
		$this->e5->manyToMany2->add($this->e1);

		$this->assertSame(array(2,3,4,5), $this->e1->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e2->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e3->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e4->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e5->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e1->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e2->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e3->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e4->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e5->manyToMany2->get()->fetchPairs(NULL, 'id'));

		$this->e3->manyToMany2->remove($this->e1);
		$this->e3->manyToMany2->add($this->e2);
		$this->e4->manyToMany2->remove($this->e1);
		$this->e4->manyToMany2->add($this->e2);

		$this->assertSame(array(2,5), $this->e1->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(3,4), $this->e2->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e3->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e4->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e5->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e1->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e2->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(2), $this->e3->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(2), $this->e4->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e5->manyToMany2->get()->fetchPairs(NULL, 'id'));
	}

	public function test5()
	{
		$this->e1->fireEvent('onLoad', $this->r, array('id' => 1, 'manyToMany1' => array(2,3,4,5)));
		$this->e3->manyToMany2->remove($this->e1);
		$this->e3->manyToMany2->add($this->e2);
		$this->e4->manyToMany2->remove($this->e1);
		$this->e4->manyToMany2->add($this->e2);

		$this->assertSame(array(2,5), $this->e1->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(3,4), $this->e2->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e3->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e4->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e5->manyToMany1->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e1->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e2->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(2), $this->e3->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(2), $this->e4->manyToMany2->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e5->manyToMany2->get()->fetchPairs(NULL, 'id'));
	}

}
