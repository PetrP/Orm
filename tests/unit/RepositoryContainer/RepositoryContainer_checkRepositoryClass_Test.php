<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../boot.php';

/**
 * @covers Orm\RepositoryContainer::checkRepositoryClass
 */
class RepositoryContainer_checkRepositoryClass_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
	}

	private function t($rn)
	{
		return $this->m->getRepository($rn);
	}

	public function testUnexist()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository 'unexists' doesn't exists");
		$this->t('unexists');
	}

	public function testImplement()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository 'repositorycontainer_checkrepositoryclass_bad' must implement Orm\\IRepository");
		$this->t('RepositoryContainer_checkRepositoryClass_Bad');
	}

	public function testAbstract()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository 'repositorycontainer_checkrepositoryclass_bad2' is abstract.");
		$this->t('RepositoryContainer_checkRepositoryClass_Bad2');
	}

	public function testEmpty()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository '' doesn't exists");
		$this->t('');
	}

	public function testOk()
	{
		$this->assertInstanceOf('TestsRepository', $this->t('tests'));
	}

	public function testIntefrace()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository 'i' doesn't exists");
		$this->t('i');
	}
}
