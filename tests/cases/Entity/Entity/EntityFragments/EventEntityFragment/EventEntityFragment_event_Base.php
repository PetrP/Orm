<?php

use Orm\RepositoryContainer;

class EventEntityFragment_event_Base extends TestCase
{
	protected $e;
	protected $r;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->e = new EventEntityFragment_Entity;
		$this->r = $m->EventEntityFragmentRepository;
	}

}
