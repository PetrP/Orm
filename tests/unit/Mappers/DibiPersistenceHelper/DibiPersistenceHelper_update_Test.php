<?php

/**
 * @covers Orm\DibiPersistenceHelper::update
 */
class DibiPersistenceHelper_update_Test extends DibiPersistenceHelper_Test
{

	public function test()
	{
		$this->d->addExpected('query', true, "UPDATE `table` SET `id`=3, `aaa`='aaa' WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$r = $this->h->call('update', array(array('id' => 3, 'aaa' => 'aaa'), 3));
		$this->assertSame(NULL, $r);
	}

}
