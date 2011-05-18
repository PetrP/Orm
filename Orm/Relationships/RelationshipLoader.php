<?php

namespace Orm;

use Nette\Object;
use Nette\InvalidStateException;
use Exception;

require_once dirname(__FILE__) . '/IRelationship.php';

class RelationshipLoader extends Object implements IEntityInjectionLoader
{
	/** @var string */
	private $class;

	/** @var string */
	private $repository;

	/** @var string */
	private $param;

	/** @var string */
	private $parentParam;

	/** @var bool */
	private $mappedByThis;

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
	 * @param bool|NULL
	 */
	public function __construct($relationship, $class, $repositoryName, $param, $entityName, $parentParam, $mappedByThis = NULL)
	{
		$mainClass = $relationship === MetaData::ManyToMany ? 'Orm\ManyToMany' : 'Orm\OneToMany';
		$oldMainClass = $relationship === MetaData::ManyToMany ? 'Orm\OldManyToMany' : 'Orm\OldOneToMany';
		if (!class_exists($class))
		{
			throw new InvalidStateException("{$entityName}::\${$parentParam} {{$relationship}} excepts $mainClass class as type, class '$class' doesn't exists");
		}
		$parents = class_parents($class);
		if (strtolower($class) !== strtolower($mainClass) AND !isset($parents[$mainClass]))
		{
			throw new InvalidStateException("{$entityName}::\${$parentParam} {{$relationship}} Class '$class' isn't instanceof $mainClass");
		}
		if (isset($parents[$oldMainClass]))
		{
			if ($repositoryName)
			{
				throw new InvalidStateException("{$entityName}::\${$parentParam} {{$relationship}} You can't specify foreign repository for $oldMainClass");
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
			else if (!RepositoryContainer::get()->isRepository($repositoryName)) // todo di
			{
				throw new InvalidStateException("$repositoryName isn't repository in {$entityName}::\${$parentParam}");
			}
		}
		if ($relationship === MetaData::ManyToMany)
		{
			if ($param)
			{
				$this->checkParams = array($relationship, $entityName);
			}
			else
			{
				$mappedByThis = true;
			}
		}

		$this->class = $class;
		$this->repository = $repositoryName;
		$this->parentParam = $parentParam;
		$this->param = $param;
		$this->mappedByThis = (bool) $mappedByThis;
	}

	/**
	 * Kontroluje asociace z druhe strany
	 */
	public function check()
	{
		if (!$this->checkParams) return;
		list($relationship, $entityName) = $this->checkParams;
		$lowerEntityName = strtolower($entityName);
		$this->checkParams = NULL;
		$param = $this->param;
		$parentParam = $this->parentParam;
		if ($relationship === MetaData::ManyToMany AND $param)
		{
			$classes = array_values((array) RepositoryContainer::get()->getRepository($this->repository)->getEntityClassName()); // todo di
			$this->canConnectWith = array_combine(array_map('strtolower', $classes), $classes);
			foreach ($this->canConnectWith as $lowerEn => $en)
			{
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
						if (!isset($loader->canConnectWith[$lowerEntityName]))
						{
							throw new InvalidStateException("{$entityName}::\${$parentParam} {{$relationship}} na druhe strane asociace {$en}::\${$param} neukazuje zpet; ukazuje na jiny repository ({$loader->repository})");
						}
						if ($loader->param !== $parentParam)
						{
							throw new InvalidStateException("{$entityName}::\${$parentParam} {{$relationship}} na druhe strane asociace {$en}::\${$param} neukazuje zpet; ukazuje na jiny parametr ({$loader->param})");
						}
						if ($this->mappedByThis === true AND $loader->mappedByThis === true)
						{
							throw new InvalidStateException("{$entityName}::\${$parentParam} a {$en}::\${$param} {{$relationship}} u ubou je nastaveno ze se na jeho strane ma mapovat, je potreba vybrat a mapovat jen podle jedne strany");
						}
						if ($this->mappedByThis === false AND $loader->mappedByThis === false)
						{
							throw new InvalidStateException("{$entityName}::\${$parentParam} a {$en}::\${$param} {{$relationship}} ani u jednoho neni nastaveno ze se podle neho ma mapovat. např: {m:m {$this->repository} {$this->param} mapped}");
						}
						continue;
					} catch (Exception $e) {}
				}
				unset($this->canConnectWith[$lowerEn]);
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
		return new $className($parent, $this->repository, $this->param, $this->parentParam, $this->mappedByThis, $value);
	}

}
