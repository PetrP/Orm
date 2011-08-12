<?php

/**
 * @covers Orm\DataSourceCollection::getConnventionalKey
 */
class DataSourceCollection_getConnventionalKey_Test extends DataSourceCollection_Base_Test
{

	private function t($key)
	{
		return DataSourceCollection_DataSourceCollection::call($this->c, 'getConnventionalKey', array($key));
	}

	public function test()
	{
		$this->assertSame('foo', $this->t('foo'));
		$this->assertSame('foo_bar_foo', $this->t('fooBarFoo'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'getConnventionalKey');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
