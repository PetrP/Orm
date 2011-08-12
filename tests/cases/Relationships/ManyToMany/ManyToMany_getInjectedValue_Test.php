<?php

use Orm\DibiManyToManyMapper;

/**
 * @covers Orm\ManyToMany::getInjectedValue
 * @covers Orm\ArrayManyToManyMapper::getValue
 */
class ManyToMany_getInjectedValue_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->assertSame(array(10=>10,11=>11,12=>12,13=>13), $this->m2m->getInjectedValue());
	}

	public function testNotArray()
	{
		$m = new DibiManyToManyMapper(new DibiConnection(array('lazy' => true)));
		$m->parentParam = 'foo';
		$m->childParam = 'bar';
		$m->table = 'foobar';
		$this->e->generatingRepository->mapper->mmm = $m;
		$this->assertNull($this->m2m->getInjectedValue());
	}

}
