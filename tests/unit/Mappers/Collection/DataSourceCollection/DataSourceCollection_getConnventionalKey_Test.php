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

}
