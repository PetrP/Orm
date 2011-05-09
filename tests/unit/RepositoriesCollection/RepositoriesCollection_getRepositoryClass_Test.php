<?php

require_once dirname(__FILE__) . '/../../boot.php';

/**
 * @covers RepositoriesCollection::getRepositoryClass
 */
class RepositoriesCollection_getRepositoryClass_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new Model;
	}

	private function t($rn)
	{
		$l = new RepositoriesCollection_getRepositoryClass;
		$l->register();
		try {
			$r = $this->m->getRepository($rn);
			$l->unregister();
			return get_class($r);
		} catch (InvalidStateException $e) {
			$l->unregister();
			if ($l->last) return $l->last;
			throw $e;
		}
		$l->unregister();
		throw new InvalidStateException;
	}

	public function test()
	{
		$this->assertSame('XyzRepository', $this->t('xyz'));
		$this->assertSame('TestsRepository', $this->t('tests'));
	}

	public function testEmpty()
	{
		$this->setExpectedException('InvalidStateException', "Repository '' doesn't exists");
		$this->t('');
	}
}
