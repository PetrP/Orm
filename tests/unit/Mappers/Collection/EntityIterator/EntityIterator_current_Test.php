<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\EntityIterator::current
 */
class EntityIterator_current_Test extends EntityIterator_Base_Test
{

	public function test()
	{
		$this->d->count = 2;
		$this->i->next();
		$this->assertSame($this->r->getById(1), $this->i->current());
		$this->i->next();
		$this->assertSame($this->r->getById(2), $this->i->current());
		$this->i->next();
		$this->assertSame(false, $this->i->valid());
	}

	public function testEmpty()
	{
		$this->d->count = 0;
		$this->i->next();
		$this->assertSame(false, $this->i->valid());
	}

}
