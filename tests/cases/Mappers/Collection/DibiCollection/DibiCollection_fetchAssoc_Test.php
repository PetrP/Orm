<?php

/**
 * @covers Orm\DibiCollection::fetchAssoc
 */
class DibiCollection_fetchAssoc_Test extends DibiCollection_BaseConnected_Test
{

	public function test()
	{
		$this->e(3);
		$this->assertSame(array(
			1 => 'boo',
			2 => 'foo',
			3 => 'bar',
		), $this->c->fetchAssoc('id=string'));
	}

}
