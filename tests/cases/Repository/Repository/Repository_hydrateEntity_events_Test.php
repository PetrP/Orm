<?php

use Orm\RepositoryContainer;
use Orm\Events;
use Orm\EventArguments;

/**
 * @covers Orm\Repository::hydrateEntity
 */
class Repository_hydrateEntity_events_Test extends TestCase
{
	public function testEventsChangeId()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$r->events->addCallbackListener(Events::LOAD, function (EventArguments $args) {
			if ($args->data['id'] == 1)
			{
				$args->data['id'] = 55;
			}
		});
		$this->assertSame(NULL, $r->getById(1));
		$this->assertSame(55, $r->getById(55)->id);
	}

	public function testEventsChangeId2()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$r->events->addCallbackListener(Events::LOAD, function (EventArguments $args) {
			if ($args->data['id'] == 111)
			{
				$args->data['id'] = 555;
			}
		});

		$e = $r->hydrateEntity(array('id' => 111));
		$this->assertSame(555, $e->id);
		$this->assertSame(NULL, $r->getById(111));
		$this->assertSame($e, $r->getById(555));
	}

}
