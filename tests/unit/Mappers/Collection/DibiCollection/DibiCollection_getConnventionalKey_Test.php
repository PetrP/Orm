<?php

/**
 * @covers Orm\DibiCollection::getConnventionalKey
 */
class DibiCollection_getConnventionalKey_Test extends DibiCollection_Base_Test
{

	private function t($key)
	{
		return DibiCollection_DibiCollection::call($this->c, 'getConnventionalKey', array($key));
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
