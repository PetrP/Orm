<?php

/**
 * @covers Orm\RepositoryContainer::getRepositoryClass
 */
class RepositoryContainer_getRepositoryClass_Test extends TestCase
{
	private function t($rn)
	{
		$m = new RepositoryContainer_getRepositoryClass;
		return $m->__getRepositoryClass($rn, true);
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

	public function testDeprecated1()
	{
		$m = new RepositoryContainer_getRepositoryClass;
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\RepositoryContainer::getRepositoryClass() is deprecated; repositoryName is deprecated; use class name instead');
		$m->__getRepositoryClass('xxx');
	}

	public function testDeprecated2()
	{
		$m = new RepositoryContainer_getRepositoryClass;
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\RepositoryContainer::getRepositoryClass() is deprecated; repositoryName is deprecated; use class name instead');
		$m->__getRepositoryClass('xxx', false);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RepositoryContainer', 'getRepositoryClass');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
