<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

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

}
