<?php

require_once dirname(__FILE__) . '/IRelationship.php';

class RelationshipLoader extends Object implements IEntityInjectionLoader
{
	/** @var string */
	private $class;

	/** @var string */
	private $repository;

	/** @var string */
	private $param;

	/**
	 * @see self::check()
	 * @var array|NULL
	 */
	private $checkParams;

	/**
	 * @see self::check()
	 * @var array entityName => entityName
	 */
	private $canConnectWith = array();

	/**
	 * @param MetaData::ManyToMany|MetaData::OneToMany
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	public function __construct($relationship, $class, $repositoryName, $param, $entityName, $parentParam)
	{
		$mainClass = $relationship === MetaData::ManyToMany ? 'ManyToMany' : 'OneToMany';
		if (!class_exists($class))
		{
			throw new InvalidStateException("{$entityName}::\${$parentParam} {{$relationship}} excepts $mainClass class as type, class '$class' doesn't exists");
		}
		$parents = class_parents($class);
		if (strtolower($class) !== strtolower($mainClass) AND !isset($parents[$mainClass]))
		{
			throw new InvalidStateException("{$entityName}::\${$parentParam} {{$relationship}} Class '$class' isn't instanceof $mainClass");
		}

		if (isset($parents["Old$mainClass"]))
		{
			if ($repositoryName)
			{
				throw new InvalidStateException("{$entityName}::\${$parentParam} {{$relationship}} You can't specify foreign repository for Old$mainClass");
			}
		}
		else
		{
			if ($relationship === MetaData::OneToMany AND !$param)
			{
				$param = $entityName;
				if ($param{0} != '_') $param{0} = $param{0} | "\x20"; // lcfirst
			}
			if (!$repositoryName)
			{
				throw new InvalidStateException("{$entityName}::\${$parentParam} {{$relationship}} You must specify foreign repository {{$relationship} repositoryName param}");
			}
			else if (!RepositoriesCollection::get()->isRepository($repositoryName)) // todo di
			{
				throw new InvalidStateException("$repositoryName isn't repository in {$entityName}::\${$parentParam}");
			}
		}
		if ($relationship === MetaData::ManyToMany AND $param)
		{
			$this->checkParams = array($relationship, $entityName, $parentParam);
		}

		$this->class = $class;
		$this->repository = $repositoryName;
		$this->param = $param;
	}

	/**
	 * Kontroluje asociace z druhe strany
	 */
	public function check()
	{
		if (!$this->checkParams) return;
		list($relationship, $entityName, $parentParam) = $this->checkParams;
		$this->checkParams = NULL;
		$param = $this->param;
		if ($relationship === MetaData::ManyToMany AND $param)
		{
			$this->canConnectWith = array();
			foreach ((array) RepositoriesCollection::get()->getRepository($this->repository)->getEntityClassName() as $en) // todo di
			{
				$this->canConnectWith[$en] = $en;
				$meta = MetaData::getEntityRules($en);
				$e = NULL;
				if (isset($meta[$param]))
				{
					$meta = $meta[$param];
					try {
						if ($meta['relationship'] !== MetaData::ManyToMany)
						{
							throw new InvalidStateException("{$entityName}::\${$parentParam} {{$relationship}} na druhe strane asociace {$en}::\${$param} neni asociace ktera by ukazovala zpet");
						}
						$loader = $meta['relationshipParam'];
						if (!$loader->param)
						{
							throw new InvalidStateException("{$entityName}::\${$parentParam} {{$relationship}} na druhe strane asociace {$en}::\${$param} neni vyplnen param ktery by ukazoval zpet");
						}
						if (!isset($loader->canConnectWith[$entityName]))
						{
							throw new InvalidStateException("{$entityName}::\${$parentParam} {{$relationship}} na druhe strane asociace {$en}::\${$param} neukazuje zpet; ukazuje na jiny repository ({$loader->repository})");
						}
						if ($loader->param !== $parentParam)
						{
							throw new InvalidStateException("{$entityName}::\${$parentParam} {{$relationship}} na druhe strane asociace {$en}::\${$param} neukazuje zpet; ukazuje na jiny parametr ({$loader->param})");
						}
						continue;
					} catch (Exception $e) {}
				}
				unset($this->canConnectWith[$en]);
				if ($e) throw $e;
			}
			if (!$this->canConnectWith)
			{
				throw new InvalidStateException("{$entityName}::\${$parentParam} {{$relationship}} na druhe strane asociace {$this->repository}::\${$param} neni asociace ktera by ukazovala zpet");
			}
		}
	}

	/**
	 * @param string
	 * @param IEntity
	 * @param mixed
	 * @return IRelationship
	 */
	public function create($className, IEntity $parent, $value = NULL)
	{
		if ($this->class !== $className) throw new InvalidStateException();
		return new $className($parent, $this->repository, $this->param, $value);
	}

}
