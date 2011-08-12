<?php

/**
 * @covers Orm\RepositoryContainer::getRepositoryClass
 */
class RepositoryContainer_getRepositoryClass_Test extends TestCase
{
	private function t($rn)
	{
		$m = new RepositoryContainer_getRepositoryClass;
		return $m->getRepositoryClass($rn);
	}

	public function test()
	{
		$this->assertSame('XyzRepository', $this->t('xyz'));
		$this->assertSame('TestsRepository', $this->t('tests'));
	}

	public function testEmpty()
	{
		$this->assertSame(NULL, $this->t(''));
		$this->assertSame(NULL, $this->t(NULL));
		$this->assertSame(NULL, $this->t(0));
		$this->t('');
	}
}
