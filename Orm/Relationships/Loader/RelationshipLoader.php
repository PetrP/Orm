<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Exception;

/**
 * Factory for IRelationship.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships\Loader
 */
class RelationshipLoader extends Object implements IEntityInjectionLoader
{
	/** Na teto strane */
	const MAPPED_HERE = true;

	/** Na druhe strane */
	const MAPPED_THERE = false;

	/** Relace ukazuje na sebe. Oba jsou stejny. Oba mapuji. */
	const MAPPED_BOTH = 2;

	/** @var string */
	private $class;

	/** @var string */
	private $repository;

	/** @var string */
	private $param;

	/** @var string */
	private $parentParam;

	/** @var RelationshipLoader::MAPPED_* */
	private $mapped;

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
	 * @param string MetaData::ManyToMany|MetaData::OneToMany
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param mixed RelationshipLoader::MAPPED_HERE|RelationshipLoader::MAPPED_THERE|NULL
	 */
	public function __construct($relationship, $class, $repositoryName, $param, $entityName, $parentParam, $mapped = NULL)
	{
		$mainClass = $relationship === MetaData::ManyToMany ? 'Orm\ManyToMany' : 'Orm\OneToMany';
		$oldMainClass = $relationship === MetaData::ManyToMany ? 'Orm\OldManyToMany' : 'Orm\OldOneToMany';
		if (!class_exists($class))
		{
			throw new RelationshipLoaderException("{$entityName}::\${$parentParam} {{$relationship}} excepts $mainClass class as type, class '$class' doesn't exists");
		}
		$parents = class_parents($class);
		if (strtolower($class) !== strtolower($mainClass) AND !isset($parents[$mainClass]))
		{
			throw new RelationshipLoaderException("{$entityName}::\${$parentParam} {{$relationship}} Class '$class' isn't instanceof $mainClass");
		}
		if (isset($parents[$oldMainClass]))
		{
			if ($repositoryName)
			{
				throw new RelationshipLoaderException("{$entityName}::\${$parentParam} {{$relationship}} You can't specify foreign repository for $oldMainClass");
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
				throw new RelationshipLoaderException("{$entityName}::\${$parentParam} {{$relationship}} You must specify foreign repository {{$relationship} repositoryName param}");
			}
			$this->checkParams = array($relationship, $entityName);
		}
		if ($relationship === MetaData::ManyToMany AND !$param)
		{
			$mapped = self::MAPPED_HERE;
		}

		$this->class = $class;
		$this->repository = $repositoryName;
		$this->parentParam = $parentParam;
		$this->param = $param;
		$this->mapped = (bool) $mapped;
	}

	/**
	 * Kontroluje asociace z druhe strany
	 * @param IRepositoryContainer
	 */
	public function check(IRepositoryContainer $model)
	{
		if (!$this->checkParams) return;
		list($relationship, $entityName) = $this->checkParams;
		$this->checkParams = NULL;
		$param = $this->param;
		$parentParam = $this->parentParam;

		if (!$model->isRepository($this->repository))
		{
			throw new RelationshipLoaderException("{$this->repository} isn't repository in {$entityName}::\${$parentParam}");
		}

		if ($relationship === MetaData::ManyToMany AND $param)
		{
			$lowerEntityName = strtolower($entityName);
			$classes = array_values((array) $model->getRepository($this->repository)->getEntityClassName());
			$this->canConnectWith = array_combine(array_map('strtolower', $classes), $classes);
			foreach ($this->canConnectWith as $lowerEn => $en)
			{
				$meta = MetaData::getEntityRules($en, $model);
				$e = NULL;
				if (isset($meta[$param]))
				{
					$meta = $meta[$param];
					try {
						if ($meta['relationship'] !== MetaData::ManyToMany)
						{
							throw new RelationshipLoaderException("{$entityName}::\${$parentParam} {{$relationship}} na druhe strane asociace {$en}::\${$param} neni asociace ktera by ukazovala zpet");
						}
						$loader = $meta['relationshipParam'];
						if (!$loader->param)
						{
							throw new RelationshipLoaderException("{$entityName}::\${$parentParam} {{$relationship}} na druhe strane asociace {$en}::\${$param} neni vyplnen param ktery by ukazoval zpet");
						}
						if (!isset($loader->canConnectWith[$lowerEntityName]))
						{
							throw new RelationshipLoaderException("{$entityName}::\${$parentParam} {{$relationship}} na druhe strane asociace {$en}::\${$param} neukazuje zpet; ukazuje na jiny repository ({$loader->repository})");
						}
						if ($loader->param !== $parentParam)
						{
							throw new RelationshipLoaderException("{$entityName}::\${$parentParam} {{$relationship}} na druhe strane asociace {$en}::\${$param} neukazuje zpet; ukazuje na jiny parametr ({$loader->param})");
						}
						if ($this === $loader)
						{
							$this->mapped = self::MAPPED_BOTH;
						}
						else
						{
							if ($this->mapped === self::MAPPED_HERE AND $loader->mapped === self::MAPPED_HERE)
							{
								throw new RelationshipLoaderException("{$entityName}::\${$parentParam} a {$en}::\${$param} {{$relationship}} u ubou je nastaveno ze se na jeho strane ma mapovat, je potreba vybrat a mapovat jen podle jedne strany");
							}
							if ($this->mapped === self::MAPPED_THERE AND $loader->mapped === self::MAPPED_THERE)
							{
								throw new RelationshipLoaderException("{$entityName}::\${$parentParam} a {$en}::\${$param} {{$relationship}} ani u jednoho neni nastaveno ze se podle neho ma mapovat. např: {m:m {$this->repository} {$this->param} mapped}");
							}
						}
						continue;
					} catch (Exception $e) {}
				}
				unset($this->canConnectWith[$lowerEn]);
				if ($e) throw $e;
			}
			if (!$this->canConnectWith)
			{
				throw new RelationshipLoaderException("{$entityName}::\${$parentParam} {{$relationship}} na druhe strane asociace {$this->repository}::\${$param} neni asociace ktera by ukazovala zpet");
			}
		}
	}

	/**
	 * @param string
	 * @param IEntity
	 * @param mixed
	 * @return IRelationship
	 */
	public function create($className, IEntity $parent, $value)
	{
		if ($this->class !== $className) throw new RelationshipLoaderException();
		return new $className($parent, $this->repository, $this->param, $this->parentParam, $this->mapped, $value);
	}

	/** @return string */
	final public function getRepository()
	{
		return $this->repository;
	}

	/** @return string */
	final public function getParam()
	{
		return $this->param;
	}

	/** @return string */
	final public function getParentParam()
	{
		return $this->parentParam;
	}

	/** @return mixed RelationshipLoader::MAPPED_* */
	final public function getWhereIsMapped()
	{
		return $this->mapped;
	}
}
