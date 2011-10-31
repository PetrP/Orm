<?php

use Orm\Events;

abstract class Events_TestCase extends TestCase
{
	public function dataProviderAll()
	{
		return array(
			array(Events::HYDRATE_BEFORE),
			array(Events::HYDRATE_AFTER),
			array(Events::ATTACH),
			array(Events::PERSIST_BEFORE),
			array(Events::PERSIST_BEFORE_UPDATE),
			array(Events::PERSIST_BEFORE_INSERT),
			array(Events::PERSIST),
			array(Events::PERSIST_AFTER_UPDATE),
			array(Events::PERSIST_AFTER_INSERT),
			array(Events::PERSIST_AFTER),
			array(Events::REMOVE_BEFORE),
			array(Events::REMOVE_AFTER),
			array(Events::FLUSH_BEFORE),
			array(Events::FLUSH_AFTER),
			array(Events::CLEAN_BEFORE),
			array(Events::CLEAN_AFTER),
			array(Events::SERIALIZE_BEFORE),
			array(Events::SERIALIZE_AFTER),
			array(Events::SERIALIZE_CONVENTIONAL),
		);
	}
}
