<?php

use Orm\Object;
use Orm\EventArguments;
use Orm\IListener;
use Orm\IListenerHydrateBefore;
use Orm\IListenerHydrateAfter;
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
use Orm\IListenerFlushBefore;
use Orm\IListenerFlushAfter;
use Orm\IListenerCleanBefore;
use Orm\IListenerCleanAfter;
use Orm\IListenerSerializeBefore;
use Orm\IListenerSerializeAfter;
use Orm\IListenerSerializeConventional;

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
class Events_addListener_Hydrate_before extends Events_addListener_Base implements IListenerHydrateBefore
{
	public function onBeforeHydrateEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Hydrate_after extends Events_addListener_Base implements IListenerHydrateAfter
{
	public function onAfterHydrateEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
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
class Events_addListener_Flush_before extends Events_addListener_Base implements IListenerFlushBefore
{
	public function onBeforeFlushEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Flush_after extends Events_addListener_Base implements IListenerFlushAfter
{
	public function onAfterFlushEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Clean_before extends Events_addListener_Base implements IListenerCleanBefore
{
	public function onBeforeCleanEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Clean_after extends Events_addListener_Base implements IListenerCleanAfter
{
	public function onAfterCleanEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Serialize_before extends Events_addListener_Base implements IListenerSerializeBefore
{
	public function onBeforeSerializeEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Serialize_after extends Events_addListener_Base implements IListenerSerializeAfter
{
	public function onAfterSerializeEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
class Events_addListener_Serialize_conventional extends Events_addListener_Base implements IListenerSerializeConventional
{
	public function onConventionalSerializeEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}

class Events_addListener_Remove extends Events_addListener_Base implements IListenerRemoveBefore, IListenerRemoveAfter
{
	public function onBeforeRemoveEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }

	public function onAfterRemoveEvent(EventArguments $args) { $this->log(__FUNCTION__, $args); }
}
