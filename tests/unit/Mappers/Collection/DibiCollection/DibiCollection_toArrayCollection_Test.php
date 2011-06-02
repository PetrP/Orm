<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DibiCollection::toArrayCollection
 */
class DibiCollection_toArrayCollection_Test extends DibiCollection_BaseConnected_Test
{

	public function testOk()
	{
		$this->e(3);
		$a = $this->c->toArrayCollection();
		$this->assertInstanceOf('Orm\ArrayCollection', $a);
		$this->assertSame(3, count($a));

		$this->d->addExpected('seek', true, 0);
		$this->d->addExpected('fetch', array('id' => 1), true);
		$this->d->addExpected('fetch', array('id' => 2), true);
		$this->d->addExpected('fetch', array('id' => 3), true);
		$this->d->addExpected('fetch', false, true);
		$this->assertSame($a->fetchAll(), $this->c->fetchAll());
	}

	public function testNoRow()
	{
		$this->e(0);
		$this->assertSame(0, $this->c->toArrayCollection()->count());
	}

}