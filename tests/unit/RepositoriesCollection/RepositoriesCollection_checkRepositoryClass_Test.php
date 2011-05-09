<?php

require_once dirname(__FILE__) . '/../../boot.php';

/**
 * @covers RepositoriesCollection::checkRepositoryClass
 */
class RepositoriesCollection_checkRepositoryClass_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new Model;
	}

	private function t($rn)
	{
		return $this->m->getRepository($rn);
	}

	public function testUnexist()
	{
		$this->setExpectedException('InvalidStateException', "Repository 'unexists' doesn't exists");
		$this->t('unexists');
	}

	public function testImplement()
	{
		$this->setExpectedException('InvalidStateException', "Repository 'repositoriescollection_checkrepositoryclass_bad' must implement IRepository");
		$this->t('RepositoriesCollection_checkRepositoryClass_Bad');
	}

	public function testAbstract()
	{
		$this->setExpectedException('InvalidStateException', "Repository 'repositoriescollection_checkrepositoryclass_bad2' is abstract.");
		$this->t('RepositoriesCollection_checkRepositoryClass_Bad2');
	}

	public function testEmpty()
	{
		$this->setExpectedException('InvalidStateException', "Repository '' doesn't exists");
		$this->t('');
	}

	public function testOk()
	{
		$this->assertInstanceOf('TestsRepository', $this->t('tests'));
	}

	public function testIntefrace()
	{
		$this->setExpectedException('InvalidStateException', "Repository 'i' doesn't exists");
		$this->t('i');
	}
}
