<?php

use Orm\RepositoryContainer;

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

	public function testUnexist2()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository 'unexistsrepository' doesn't exists");
		$this->t('unexistsRepository');
	}

	public function testImplement()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository 'RepositoryContainer_checkRepositoryClass_BadRepository' must implement Orm\\IRepository");
		$this->t('RepositoryContainer_checkRepositoryClass_Bad');
	}

	public function testImplement2()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository 'RepositoryContainer_checkRepositoryClass_BadRepository' must implement Orm\\IRepository");
		$this->t('RepositoryContainer_checkRepositoryClass_BadRepository');
	}

	public function testAbstract()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository 'RepositoryContainer_checkRepositoryClass_Bad2Repository' is abstract.");
		$this->t('RepositoryContainer_checkRepositoryClass_Bad2');
	}

	public function testAbstract2()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository 'RepositoryContainer_checkRepositoryClass_Bad2Repository' is abstract.");
		$this->t('RepositoryContainer_checkRepositoryClass_Bad2Repository');
	}

	public function testNotInstantiable()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository 'RepositoryContainer_checkRepositoryClass_Bad3Repository' isn't instantiable");
		$this->t('RepositoryContainer_checkRepositoryClass_Bad3');
	}

	public function testNotInstantiable2()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository 'RepositoryContainer_checkRepositoryClass_Bad3Repository' isn't instantiable");
		$this->t('RepositoryContainer_checkRepositoryClass_Bad3Repository');
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

	public function testOk2()
	{
		$this->assertInstanceOf('TestsRepository', $this->t('testsRepository'));
	}

	public function testIntefrace()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository 'orm\\i' doesn't exists");
		$this->t('Orm\\I');
	}

	public function testIntefrace2()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Repository 'orm\\irepository' doesn't exists");
		$this->t('Orm\\IRepository');
	}
}
