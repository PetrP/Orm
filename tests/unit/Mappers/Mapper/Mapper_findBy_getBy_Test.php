<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Mapper::findBy
 * @covers Mapper::getBy
 */
class Mapper_findBy_getBy_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		Mapper_call_Collection::$last = NULL;
		$this->m = new Mapper_call_Mapper(new TestsRepository(new Model));
	}
	
	private function t(array $a, $m = 'findBy')
	{
		$this->assertSame($m == 'findBy' ? 'qwe' : 'zxc' , $this->m->$m($a));
		$this->assertSame(array($m, $a), Mapper_call_Collection::$last);
	}

	public function testFindBy1()
	{
		$this->t(array('name' => 'abc'));
	}

	public function testGetBy()
	{
		$this->t(array('name' => 'abc'), 'getBy');
	}

	public function testFindBy2()
	{
		$this->t(array('name' => 'abc', 'id' => array(10,11,12)));
	}

}