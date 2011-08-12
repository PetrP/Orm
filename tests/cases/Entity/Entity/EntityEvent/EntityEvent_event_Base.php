<?php

use Orm\RepositoryContainer;

class EntityEvent_event_Base extends TestCase
{
	protected $e;
	protected $r;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->e = new EntityEvent_Entity;
		$this->r = $m->EntityEvent;
	}

}
