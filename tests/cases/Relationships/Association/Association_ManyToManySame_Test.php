<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ManyToMany::add
 * @covers Orm\ManyToMany::remove
 */
class Association_ManyToManySame_Test extends TestCase
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
		$this->e1->manyToManySame->add($this->e2);
		$this->e1->manyToManySame->add($this->e3);
		$this->e1->manyToManySame->add($this->e4);
		$this->e1->manyToManySame->add($this->e5);
		$this->assertSame(array(2,3,4,5), $this->e1->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e2->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e3->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e4->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e5->manyToManySame->get()->fetchPairs(NULL, 'id'));
	}

	public function test2()
	{
		$this->e2->manyToManySame->add($this->e1);
		$this->e3->manyToManySame->add($this->e1);
		$this->e4->manyToManySame->add($this->e1);
		$this->e5->manyToManySame->add($this->e1);
		$this->assertSame(array(2,3,4,5), $this->e1->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e2->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e3->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e4->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e5->manyToManySame->get()->fetchPairs(NULL, 'id'));
	}

	public function test3()
	{
		$this->e2->manyToManySame->add($this->e1);
		$this->e1->manyToManySame->add($this->e3);
		$this->e1->manyToManySame->add($this->e4);
		$this->e5->manyToManySame->add($this->e1);

		$this->assertSame(array(2,3,4,5), $this->e1->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e2->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e3->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e4->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e5->manyToManySame->get()->fetchPairs(NULL, 'id'));

		$this->e2->manyToManySame->remove($this->e1);
		$this->e1->manyToManySame->remove($this->e3);
		$this->e1->manyToManySame->remove($this->e4);
		$this->e5->manyToManySame->remove($this->e1);

		$this->assertSame(array(), $this->e1->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e2->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e3->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e4->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(), $this->e5->manyToManySame->get()->fetchPairs(NULL, 'id'));
	}

	public function test4()
	{
		$this->e2->manyToManySame->add($this->e1);
		$this->e1->manyToManySame->add($this->e3);
		$this->e1->manyToManySame->add($this->e4);
		$this->e5->manyToManySame->add($this->e1);

		$this->assertSame(array(2,3,4,5), $this->e1->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e2->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e3->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e4->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e5->manyToManySame->get()->fetchPairs(NULL, 'id'));

		$this->e3->manyToManySame->remove($this->e1);
		$this->e3->manyToManySame->add($this->e2);
		$this->e4->manyToManySame->remove($this->e1);
		$this->e4->manyToManySame->add($this->e2);

		$this->assertSame(array(2,5), $this->e1->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1,3,4), $this->e2->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(2), $this->e3->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(2), $this->e4->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e5->manyToManySame->get()->fetchPairs(NULL, 'id'));
	}

	public function test5()
	{
		$this->e1->fireEvent('onLoad', $this->r, array('id' => 1, 'manyToManySame' => array(2,3,4,5)));
		$this->e3->manyToManySame->remove($this->e1);
		$this->e3->manyToManySame->add($this->e2);
		$this->e4->manyToManySame->remove($this->e1);
		$this->e4->manyToManySame->add($this->e2);

		$this->assertSame(array(2,5), $this->e1->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1,3,4), $this->e2->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(2), $this->e3->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(2), $this->e4->manyToManySame->get()->fetchPairs(NULL, 'id'));
		$this->assertSame(array(1), $this->e5->manyToManySame->get()->fetchPairs(NULL, 'id'));
	}

}
