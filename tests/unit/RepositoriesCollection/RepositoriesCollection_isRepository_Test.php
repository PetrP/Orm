<?php

require_once dirname(__FILE__) . '/../../boot.php';

/**
 * @covers RepositoriesCollection::isRepository
 */
class RepositoriesCollection_isRepository_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new Model;
	}

	public function test()
	{
		$this->assertTrue($this->m->isRepository('tests'));
		$this->assertTrue($this->m->isRepository('tests'));
	}

	public function test2()
	{
		$this->assertTrue($this->m->isRepository('TestS'));
		$this->assertTrue($this->m->isRepository('tESTs'));
	}

	public function test3()
	{
		$this->assertFalse($this->m->isRepository('blabla'));
		$this->assertFalse($this->m->isRepository('Xyz'));
	}
}
