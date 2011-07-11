<?php

/**
 * @covers Orm\OneToMany::getIterator
 */
class OneToMany_getIterator_Test extends OneToMany_Test
{

	public function test()
	{
		$this->assertInstanceOf('Traversable', $this->o2m->getIterator());
	}

	public function test2()
	{
		$this->assertSame($this->o2m->get()->fetchAll(), iterator_to_array($this->o2m->getIterator()));
	}

	public function test3()
	{
		$this->assertSame(4, iterator_count($this->o2m->getIterator()));
	}

}
