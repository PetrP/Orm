<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::setValueHelper
 */
class Association_OneToOneSame_Test extends TestCase
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
		$this->e1->oneToOneSame = $this->e2;
		$this->assertSame($this->e2, $this->e1->oneToOneSame);
		$this->assertSame($this->e1, $this->e2->oneToOneSame);
		$this->assertSame(NULL, $this->e3->oneToOneSame);
		$this->assertSame(NULL, $this->e4->oneToOneSame);
		$this->assertSame(NULL, $this->e5->oneToOneSame);

		$this->assertFalse(property_exists($this->e1, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e2, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e3, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e4, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e5, 'oneToOneSame'));
	}

	public function test2()
	{
		$this->e2->oneToOneSame = $this->e1;
		$this->assertSame($this->e2, $this->e1->oneToOneSame);
		$this->assertSame($this->e1, $this->e2->oneToOneSame);
		$this->assertSame(NULL, $this->e3->oneToOneSame);
		$this->assertSame(NULL, $this->e4->oneToOneSame);
		$this->assertSame(NULL, $this->e5->oneToOneSame);

		$this->assertFalse(property_exists($this->e1, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e2, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e3, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e4, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e5, 'oneToOneSame'));
	}

	public function test3()
	{
		$this->e2->oneToOneSame = $this->e1;

		$this->assertSame($this->e2, $this->e1->oneToOneSame);
		$this->assertSame($this->e1, $this->e2->oneToOneSame);
		$this->assertSame(NULL, $this->e3->oneToOneSame);
		$this->assertSame(NULL, $this->e4->oneToOneSame);
		$this->assertSame(NULL, $this->e5->oneToOneSame);

		$this->e1->oneToOneSame = NULL;

		$this->assertSame(NULL, $this->e1->oneToOneSame);
		$this->assertSame(NULL, $this->e2->oneToOneSame);
		$this->assertSame(NULL, $this->e3->oneToOneSame);
		$this->assertSame(NULL, $this->e4->oneToOneSame);
		$this->assertSame(NULL, $this->e5->oneToOneSame);

		$this->assertFalse(property_exists($this->e1, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e2, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e3, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e4, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e5, 'oneToOneSame'));
	}

	public function test4()
	{
		$this->e2->oneToOneSame = $this->e1;

		$this->assertSame($this->e2, $this->e1->oneToOneSame);
		$this->assertSame($this->e1, $this->e2->oneToOneSame);
		$this->assertSame(NULL, $this->e3->oneToOneSame);
		$this->assertSame(NULL, $this->e4->oneToOneSame);
		$this->assertSame(NULL, $this->e5->oneToOneSame);

		$this->e1->oneToOneSame = $this->e3;

		$this->assertSame($this->e3, $this->e1->oneToOneSame);
		$this->assertSame(NULL, $this->e2->oneToOneSame);
		$this->assertSame($this->e1, $this->e3->oneToOneSame);
		$this->assertSame(NULL, $this->e4->oneToOneSame);
		$this->assertSame(NULL, $this->e5->oneToOneSame);

		$this->assertFalse(property_exists($this->e1, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e2, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e3, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e4, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e5, 'oneToOneSame'));
	}

	public function test5()
	{
		$this->e2->fireEvent('onLoad', $this->r, array('id' => 2, 'oneToOneSame' => 1));
		$this->e1->fireEvent('onLoad', $this->r, array('id' => 1, 'oneToOneSame' => 2));
		$this->e1->oneToOneSame = 3;

		$this->assertSame($this->e3, $this->e1->oneToOneSame);
		$this->assertSame(NULL, $this->e2->oneToOneSame);
		$this->assertSame($this->e1, $this->e3->oneToOneSame);
		$this->assertSame(NULL, $this->e4->oneToOneSame);
		$this->assertSame(NULL, $this->e5->oneToOneSame);

		$this->assertFalse(property_exists($this->e1, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e2, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e3, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e4, 'oneToOneSame'));
		$this->assertFalse(property_exists($this->e5, 'oneToOneSame'));
	}

}
