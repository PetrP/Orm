<?php

use Orm\DibiManyToManyMapper;

/**
 * @covers Orm\DibiManyToManyMapper::__construct
 */
class DibiManyToManyMapper_construct_Test extends TestCase
{
	public function test()
	{
		$c = new DibiConnection(array('lazy' => true));
		$mm = new DibiManyToManyMapper($c);
		$this->assertInstanceOf('Orm\IManyToManyMapper', $mm);
		$this->assertAttributeSame($c, 'connection', $mm);
	}
}
