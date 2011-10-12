<?php

use Orm\Object;
use Orm\EventArguments;
use Orm\IListener;
use Orm\IListenerLoad;
use Orm\IListenerAttach;
use Orm\IListenerPersistBefore;
use Orm\IListenerPersistBeforeUpdate;
use Orm\IListenerPersistBeforeInsert;
use Orm\IListenerPersist;
use Orm\IListenerPersistAfterUpdate;
use Orm\IListenerPersistAfterInsert;
use Orm\IListenerPersistAfter;
use Orm\IListenerRemoveBefore;
use Orm\IListenerRemoveAfter;
use Orm\IEventFirer;

abstract class Events_addListener_Base extends Object
{
	static $logs = array();
	public $count = 0;
	protected function log($m, EventArguments $args)
	{
		$this->count++;
		self::$logs[] = array($this, $m, $args);
		// todo
	}
}

class Events_addListener_Event extends Events_addListener_Base implements IListener {}
class Events_addListener_Load extends Events_addListener_Base implements IListenerLoad
{
	public function onLoadEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Attach extends Events_addListener_Base implements IListenerAttach
{
	public function onAttachEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Persist_before extends Events_addListener_Base implements IListenerPersistBefore
{
	public function onBeforePersistEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Persist_before_update extends Events_addListener_Base implements IListenerPersistBeforeUpdate
{
	public function onBeforePersistUpdateEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Persist_before_insert extends Events_addListener_Base implements IListenerPersistBeforeInsert
{
	public function onBeforePersistInsertEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Persist extends Events_addListener_Base implements IListenerPersist
{
	public function onPersistEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Persist_after_update extends Events_addListener_Base implements IListenerPersistAfterUpdate
{
	public function onAfterPersistUpdateEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Persist_after_insert extends Events_addListener_Base implements IListenerPersistAfterInsert
{
	public function onAfterPersistInsertEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Persist_after extends Events_addListener_Base implements IListenerPersistAfter
{
	public function onAfterPersistEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Remove_before extends Events_addListener_Base implements IListenerRemoveBefore
{
	public function onBeforeRemoveEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Remove_after extends Events_addListener_Base implements IListenerRemoveAfter
{
	public function onAfterRemoveEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}

class Events_addListener_Firer extends Events_addListener_Base implements IEventFirer
{
	public function fireEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}

class Events_addListener_Remove extends Events_addListener_Base implements IListenerRemoveBefore, IListenerRemoveAfter
{
	public function onBeforeRemoveEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }

	public function onAfterRemoveEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}

class Events_addListener_FirerAndNormal extends Events_addListener_Base implements IEventFirer, IListenerRemoveAfter
{
	public function fireEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }

	public function onAfterRemoveEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
