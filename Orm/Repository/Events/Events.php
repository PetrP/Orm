<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events
 */
class Events extends Object
{
	/**
	 * Before data from storage are hydrated into entity.
	 * Has EventArguments::$data and EventArguments::$entity.
	 * It fires before data are hydrated. Entity is empty. $data can be changed. Values at entity will be overwritten.
	 *
	 * <code>
	 * $events->addCallbackListener(Events::LOAD_BEFORE, function (EventArguments $args) {
	 * 	$args->data['foo'] = 'data can be changed';
	 * });
	 * </code>
	 * @see IRepository::hydrateEntity()
	 * @see IListenerLoadBefore
	 */
	const LOAD_BEFORE = 1;

	/**
	 * After data from storage are hydrated into entity.
	 * Has EventArguments::$data and EventArguments::$entity.
	 * It fires after data are hydrated. Entity has all values. If $data are changed there's no effect.
	 * @see IRepository::hydrateEntity()
	 * @see IListenerLoadAfter
	 */
	const LOAD_AFTER = 2;

	/**
	 * Entity is attached to repository.
	 * Has EventArguments::$entity.
	 * @see IRepository::attach()
	 * @see IListenerAttach
	 */
	const ATTACH = 4;

	/**
	 * Entity is changed and will be saved.
	 * It fires before.
	 * Has EventArguments::$entity.
	 * @see IRepository::persist()
	 * @see IListenerPersistBefore
	 */
	const PERSIST_BEFORE = 8;

	/**
	 * Entity will be saved first time.
	 * It fires before.
	 * Has EventArguments::$entity.
	 * @see IRepository::persist()
	 * @see IListenerPersistBeforeInsert
	 */
	const PERSIST_BEFORE_INSERT = 16;

	/**
	 * Entity will be updated.
	 * It fires before.
	 * Has EventArguments::$entity.
	 * @see IRepository::persist()
	 * @see IListenerPersistBeforeUpdate
	 */
	const PERSIST_BEFORE_UPDATE = 32;

	/**
	 * Entity is saved.
	 * Has EventArguments::$id and EventArguments::$entity.
	 * $id can be changed but it's not recommended.
	 * Some relationship are not persisted yet.
	 * If entity is changed during Events::PERSIST_AFTER, this event will fired twice.
	 * @see IRepository::persist()
	 * @see IListenerPersist
	 */
	const PERSIST = 64;

	/**
	 * Entity was saved first time.
	 * It fires after.
	 * Has EventArguments::$entity.
	 * If entity is changed during this event, changes will be updated in storage and it fires Events::PERSIST again.
	 * @see IRepository::persist()
	 * @see IListenerPersistAfterInsert
	 */
	const PERSIST_AFTER_INSERT = 128;

	/**
	 * Entity was updated.
	 * It fires after.
	 * Has EventArguments::$entity.
	 * If entity is changed during this event, changes will be updated in storage and it fires Events::PERSIST again.
	 * @see IRepository::persist()
	 * @see IListenerPersistAfterUpdate
	 */
	const PERSIST_AFTER_UPDATE =  256;

	/**
	 * Entity was saved.
	 * It fires after.
	 * Has EventArguments::$entity.
	 * If entity is changed during this event, changes will be updated in storage and it fires Events::PERSIST again.
	 * @see IRepository::persist()
	 * @see IListenerPersistAfter
	 */
	const PERSIST_AFTER = 512;

	/**
	 * It fires before entity will be deleted.
	 * Has EventArguments::$entity.
	 * @see IRepository::remove()
	 * @see IListenerRemoveBefore
	 */
	const REMOVE_BEFORE = 1024;

	/**
	 * It fires after entity was deleted.
	 * Has EventArguments::$entity.
	 * @see IRepository::remove()
	 * @see IListenerRemoveAfter
	 */
	const REMOVE_AFTER = 2048;

	/**
	 * It fires before repository will be flushed.
	 * @see IRepository::flush()
	 * @see IRepositoryContainer::flush()
	 * @see IListenerFlushBefore
	 */
	const FLUSH_BEFORE = 4096;

	/**
	 * It fires after repository was flushed.
	 * @see IRepository::flush()
	 * @see IRepositoryContainer::flush()
	 * @see IListenerFlushAfter
	 */
	const FLUSH_AFTER = 8192;

	/**
	 * It fires before repository will be cleaned.
	 * @see IRepository::clean()
	 * @see IRepositoryContainer::clean()
	 * @see IListenerCleanBefore
	 */
	const CLEAN_BEFORE = 16384;

	/**
	 * It fires after repository was cleaned.
	 * @see IRepository::clean()
	 * @see IRepositoryContainer::clean()
	 * @see IListenerCleanAfter
	 */
	const CLEAN_AFTER = 32768;

	/**
	 * @var array
	 * 	event => array(array(true, callback)) // not lazy
	 * 	event => array(listenersLine => array(false, lazyKey)) // lazy
	 */
	private $listeners = array(
		self::LOAD_BEFORE => array(),
		self::LOAD_AFTER => array(),
		self::ATTACH => array(),
		self::PERSIST_BEFORE => array(),
		self::PERSIST_BEFORE_UPDATE => array(),
		self::PERSIST_BEFORE_INSERT => array(),
		self::PERSIST => array(),
		self::PERSIST_AFTER_UPDATE => array(),
		self::PERSIST_AFTER_INSERT => array(),
		self::PERSIST_AFTER => array(),
		self::REMOVE_BEFORE => array(),
		self::REMOVE_AFTER => array(),
		self::FLUSH_BEFORE => array(),
		self::FLUSH_AFTER => array(),
		self::CLEAN_BEFORE => array(),
		self::CLEAN_AFTER => array(),
	);

	/** @var array lazyKey => array(factory, array(event => listenersLine)) */
	private $lazy = array();

	/** @var IRepository */
	private $repository;

	/** @var array event => array(interface, method, entityEventMethod|NULL) */
	private static $instructions = array(
		self::LOAD_BEFORE => array('Orm\IListenerLoadBefore', 'onBeforeLoadEvent', 'onLoad'),
		self::LOAD_AFTER => array('Orm\IListenerLoadAfter', 'onAfterLoadEvent', NULL),
		self::ATTACH => array('Orm\IListenerAttach', 'onAttachEvent', 'onAttach'),
		self::PERSIST_BEFORE => array('Orm\IListenerPersistBefore', 'onBeforePersistEvent', 'onBeforePersist'),
		self::PERSIST_BEFORE_UPDATE => array('Orm\IListenerPersistBeforeUpdate', 'onBeforePersistUpdateEvent', 'onBeforeUpdate'), // todo method name
		self::PERSIST_BEFORE_INSERT => array('Orm\IListenerPersistBeforeInsert', 'onBeforePersistInsertEvent', 'onBeforeInsert'),
		self::PERSIST => array('Orm\IListenerPersist', 'onPersistEvent', 'onPersist'), // todo?
		self::PERSIST_AFTER_UPDATE => array('Orm\IListenerPersistAfterUpdate', 'onAfterPersistUpdateEvent', 'onAfterUpdate'),
		self::PERSIST_AFTER_INSERT => array('Orm\IListenerPersistAfterInsert', 'onAfterPersistInsertEvent', 'onAfterInsert'),
		self::PERSIST_AFTER => array('Orm\IListenerPersistAfter', 'onAfterPersistEvent', 'onAfterPersist'),
		self::REMOVE_BEFORE => array('Orm\IListenerRemoveBefore', 'onBeforeRemoveEvent', 'onBeforeRemove'),
		self::REMOVE_AFTER => array('Orm\IListenerRemoveAfter', 'onAfterRemoveEvent', 'onAfterRemove'),
		self::FLUSH_BEFORE => array('Orm\IListenerFlushBefore', 'onBeforeFlushEvent', NULL),
		self::FLUSH_AFTER => array('Orm\IListenerFlushAfter', 'onAfterFlushEvent', NULL),
		self::CLEAN_BEFORE => array('Orm\IListenerCleanBefore', 'onBeforeCleanEvent', NULL),
		self::CLEAN_AFTER => array('Orm\IListenerCleanAfter', 'onAfterCleanEvent', NULL),
	);

	/** @param IRepository */
	public function __construct(IRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * Adds event listener.
	 * @param IListener
	 * @return Events $this
	 */
	public function addListener(IListener $event)
	{
		$is = false;
		foreach (self::$instructions as $e => $m)
		{
			if ($event instanceof $m[0])
			{
				$is = true;
				$this->listeners[$e][] = array(true, array($event, $m[1]));
			}
		}
		if ($event instanceof IEventFirer)
		{
			$is = true;
			foreach (self::$instructions as $e => $m)
			{
				$this->listeners[$e][] = array(true, array($event, 'fireEvent'));
			}
		}
		if (!$is)
		{
			throw new InvalidArgumentException(ExceptionHelper::format(array($this, $event), "%c1::addListener() no event interface match for '%c2'."));
		}
		return $this;
	}

	/**
	 * Adds event callback listener.
	 * @param int one or more (e.g. Events::LOAD_BEFORE | Events::ATTACH)
	 * @param array|string|Closure|Callback
	 * @return Events $this
	 */
	public function addCallbackListener($type, $callback)
	{
		$is = false;
		$callback = Callback::create($callback)->getNative();
		foreach (self::$instructions as $e => $m)
		{
			if ($type & $e)
			{
				$is = true;
				$this->listeners[$e][] = array(true, $callback);
			}
		}
		if (!$is)
		{
			throw new InvalidArgumentException(ExceptionHelper::format(array($this, $type), "%c1::addCallbackListener() no event constant match for '%s2'."));
		}
		return $this;
	}

	/**
	 * Adds factory for event callback listener.
	 * @param int one or more (e.g. Events::LOAD | Events::ATTACH)
	 * @param array|string|Closure|Callback callback must return IListener
	 * @return Events $this
	 */
	public function addLazyListener($type, $factory)
	{
		$is = false;
		$factory = Callback::create($factory)->getNative();
		$key = count($this->lazy);
		$types = array();
		foreach (self::$instructions as $e => $m)
		{
			if ($type & $e)
			{
				$is = true;
				$types[$e] = count($this->listeners[$e]);
				$this->listeners[$e][] = array(false, $key);
			}
		}
		if (!$is)
		{
			throw new InvalidArgumentException(ExceptionHelper::format(array($this, $type), "%c1::addLazyListener() no event constant match for '%v2'."));
		}
		$this->lazy[$key] = array($factory, $types);
		return $this;
	}

	/**
	 * Fires event.
	 * @param int one event (e.g. Events::LOAD_BEFORE)
	 * @param IEntity|NULL
	 * @param array
	 * @return Events $this
	 * @see EventArguments
	 */
	public function fireEvent($type, IEntity $entity = NULL, array $arguments = array())
	{
		if (!isset(self::$instructions[$type]))
		{
			throw new InvalidArgumentException(array($this, 'fireEvent() $type', 'valid event type', $type));
		}
		$args = new EventArguments($type, $this->repository, $entity, $arguments);
		foreach ($this->listeners[$type] as $k => $event)
		{
			if ($event[0] === false)
			{
				$this->handleLazy($event[1]);
				$event = $this->listeners[$type][$k];
			}
			call_user_func($event[1], $args);
			$args->check(); // srozumitelna chyba na ukor vykonu
		}
		if ($entity AND self::$instructions[$type][2])
		{
			$more = NULL;
			if (isset($args->id))
			{
				$more = $args->id;
			}
			else
			{
				if (isset($args->data))
				{
					$more = $args->data;
				}
			}
			$entity->fireEvent(self::$instructions[$type][2], $this->repository, $more);
		}
		return $this;
	}

	/** @param int */
	private function handleLazy($key)
	{
		$object = call_user_func($this->lazy[$key][0]);
		if ($object instanceof IEventFirer)
		{
			throw new NotSupportedException('Orm\Events: lazy Orm\IEventFirer is not supported');
		}
		if (!($object instanceof IListener))
		{
			$cb = Callback::create($this->lazy[$key][0]);
			throw new BadReturnException(array(NULL, __CLASS__ . " lazy factory $cb()", 'Orm\IListener', $object));
		}
		$types = $this->lazy[$key][1];
		foreach (self::$instructions as $e => $m)
		{
			if (isset($types[$e]))
			{
				if ($object instanceof $m[0])
				{
					$this->listeners[$e][$types[$e]] = array(true, array($object, $m[1]));
				}
				else
				{
					throw new InvalidArgumentException(ExceptionHelper::format(array($this, Callback::create($this->lazy[$key][0]), $m[0], $object), "%c1 lazy factory %s2() must return %s3; '%v4' given."));
				}
			}
			else if ($object instanceof $m[0])
			{
				throw new InvalidArgumentException(ExceptionHelper::format(array($this, Callback::create($this->lazy[$key][0]), $m[0], $object), "%c1 lazy factory %s2() returns not expected %s3; '%v4'."));
			}
		}

		unset($this->lazy[$key]);
	}

}
