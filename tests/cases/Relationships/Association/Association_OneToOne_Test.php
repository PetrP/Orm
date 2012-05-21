<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::setValueHelper
 */
class Association_OneToOne_Test extends TestCase
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
		$this->e1->oneToOne2 = $this->e2;
		$this->assertSame(NULL, $this->e1->oneToOne1);
		$this->assertSame($this->e1, $this->e2->oneToOne1);
		$this->assertSame(NULL, $this->e3->oneToOne1);
		$this->assertSame(NULL, $this->e4->oneToOne1);
		$this->assertSame(NULL, $this->e5->oneToOne1);
		$this->assertSame($this->e2, $this->e1->oneToOne2);
		$this->assertSame(NULL, $this->e2->oneToOne2);
		$this->assertSame(NULL, $this->e3->oneToOne2);
		$this->assertSame(NULL, $this->e4->oneToOne2);
		$this->assertSame(NULL, $this->e5->oneToOne2);

		$this->assertFalse(property_exists($this->e1, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e2, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e3, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e4, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e5, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e1, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e2, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e3, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e4, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e5, 'oneToOne2'));
	}

	public function test2()
	{
		$this->e2->oneToOne1 = $this->e1;
		$this->assertSame(NULL, $this->e1->oneToOne1);
		$this->assertSame($this->e1, $this->e2->oneToOne1);
		$this->assertSame(NULL, $this->e3->oneToOne1);
		$this->assertSame(NULL, $this->e4->oneToOne1);
		$this->assertSame(NULL, $this->e5->oneToOne1);
		$this->assertSame($this->e2, $this->e1->oneToOne2);
		$this->assertSame(NULL, $this->e2->oneToOne2);
		$this->assertSame(NULL, $this->e3->oneToOne2);
		$this->assertSame(NULL, $this->e4->oneToOne2);
		$this->assertSame(NULL, $this->e5->oneToOne2);

		$this->assertFalse(property_exists($this->e1, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e2, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e3, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e4, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e5, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e1, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e2, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e3, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e4, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e5, 'oneToOne2'));
	}

	public function test3()
	{
		$this->e2->oneToOne1 = $this->e1;

		$this->assertSame(NULL, $this->e1->oneToOne1);
		$this->assertSame($this->e1, $this->e2->oneToOne1);
		$this->assertSame(NULL, $this->e3->oneToOne1);
		$this->assertSame(NULL, $this->e4->oneToOne1);
		$this->assertSame(NULL, $this->e5->oneToOne1);
		$this->assertSame($this->e2, $this->e1->oneToOne2);
		$this->assertSame(NULL, $this->e2->oneToOne2);
		$this->assertSame(NULL, $this->e3->oneToOne2);
		$this->assertSame(NULL, $this->e4->oneToOne2);
		$this->assertSame(NULL, $this->e5->oneToOne2);

		$this->e1->oneToOne2 = NULL;

		$this->assertSame(NULL, $this->e1->oneToOne1);
		$this->assertSame(NULL, $this->e2->oneToOne1);
		$this->assertSame(NULL, $this->e3->oneToOne1);
		$this->assertSame(NULL, $this->e4->oneToOne1);
		$this->assertSame(NULL, $this->e5->oneToOne1);
		$this->assertSame(NULL, $this->e1->oneToOne2);
		$this->assertSame(NULL, $this->e2->oneToOne2);
		$this->assertSame(NULL, $this->e3->oneToOne2);
		$this->assertSame(NULL, $this->e4->oneToOne2);
		$this->assertSame(NULL, $this->e5->oneToOne2);

		$this->assertFalse(property_exists($this->e1, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e2, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e3, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e4, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e5, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e1, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e2, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e3, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e4, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e5, 'oneToOne2'));
	}

	public function test4()
	{
		$this->e2->oneToOne1 = $this->e1;

		$this->assertSame(NULL, $this->e1->oneToOne1);
		$this->assertSame($this->e1, $this->e2->oneToOne1);
		$this->assertSame(NULL, $this->e3->oneToOne1);
		$this->assertSame(NULL, $this->e4->oneToOne1);
		$this->assertSame(NULL, $this->e5->oneToOne1);
		$this->assertSame($this->e2, $this->e1->oneToOne2);
		$this->assertSame(NULL, $this->e2->oneToOne2);
		$this->assertSame(NULL, $this->e3->oneToOne2);
		$this->assertSame(NULL, $this->e4->oneToOne2);
		$this->assertSame(NULL, $this->e5->oneToOne2);

		$this->e1->oneToOne2 = $this->e3;

		$this->assertSame(NULL, $this->e1->oneToOne1);
		$this->assertSame(NULL, $this->e2->oneToOne1);
		$this->assertSame($this->e1, $this->e3->oneToOne1);
		$this->assertSame(NULL, $this->e4->oneToOne1);
		$this->assertSame(NULL, $this->e5->oneToOne1);
		$this->assertSame($this->e3, $this->e1->oneToOne2);
		$this->assertSame(NULL, $this->e2->oneToOne2);
		$this->assertSame(NULL, $this->e3->oneToOne2);
		$this->assertSame(NULL, $this->e4->oneToOne2);
		$this->assertSame(NULL, $this->e5->oneToOne2);

		$this->assertFalse(property_exists($this->e1, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e2, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e3, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e4, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e5, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e1, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e2, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e3, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e4, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e5, 'oneToOne2'));
	}

	public function test5()
	{
		$this->e2->fireEvent('onLoad', $this->r, array('id' => 2, 'oneToOne1' => 1));
		$this->e1->fireEvent('onLoad', $this->r, array('id' => 1, 'oneToOne2' => 2));
		$this->e1->oneToOne2 = 3;

		$this->assertSame(NULL, $this->e1->oneToOne1);
		$this->assertSame(NULL, $this->e2->oneToOne1);
		$this->assertSame($this->e1, $this->e3->oneToOne1);
		$this->assertSame(NULL, $this->e4->oneToOne1);
		$this->assertSame(NULL, $this->e5->oneToOne1);
		$this->assertSame($this->e3, $this->e1->oneToOne2);
		$this->assertSame(NULL, $this->e2->oneToOne2);
		$this->assertSame(NULL, $this->e3->oneToOne2);
		$this->assertSame(NULL, $this->e4->oneToOne2);
		$this->assertSame(NULL, $this->e5->oneToOne2);

		$this->assertFalse(property_exists($this->e1, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e2, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e3, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e4, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e5, 'oneToOne1'));
		$this->assertFalse(property_exists($this->e1, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e2, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e3, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e4, 'oneToOne2'));
		$this->assertFalse(property_exists($this->e5, 'oneToOne2'));
	}

	public function testBug45()
	{
		$e = new Association_Entity;
		$e2 = new Association_Entity;
		$this->r->attach($e);
		$e->oneToOne1 = $e2;
		$e->oneToOne1 = NULL;

		$this->assertSame(NULL, $e->oneToOne1);
		$this->assertSame(NULL, $e2->oneToOne2);
	}

}
