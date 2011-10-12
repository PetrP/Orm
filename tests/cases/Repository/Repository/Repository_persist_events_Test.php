<?php

use Orm\RepositoryContainer;
use Orm\Events;
use Orm\EventArguments;

/**
 * @covers Orm\Repository::persist
 */
class Repository_persist_events_Test extends TestCase
{
	public function testEventsChangeId()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$r->events->addCallbackListener(Events::PERSIST, function (EventArguments $args) {
			$args->id = 55;
		});

		$e = $r->getById(1);
		$e->string = 'xxx';
		$this->assertSame($e, $r->persist($e));
		$this->assertSame(55, $e->id);

		$this->assertSame(NULL, $r->getById(1));
		$this->assertSame($e, $r->getById(55));
	}

}
