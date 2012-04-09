<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Exception;

/**
 * MetaData for Orm\IRelationship.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships\MetaData
 */
abstract class RelationshipMetaData extends Object
{

	/** @var string MetaData::ManyToOne|MetaData::OneToOne|MetaData::ManyToMany|MetaData::OneToMany */
	private $type;

	/** @var string */
	private $parentEntityName;

	/** @var string */
	private $parentParam;

	/** @var string */
	private $childRepository;

	/** @var string */
	private $childParam;

	/**
	 * @see self::check()
	 * @var array entityName => entityName
	 */
	private $canConnectWith = array();

	/**
	 * @param string MetaData::ManyToOne|MetaData::OneToOne|MetaData::ManyToMany|MetaData::OneToMany
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	public function __construct($type, $parentEntityName, $parentParam, $childRepositoryName, $childParam)
	{
		$this->type = $type;
		$this->parentEntityName = $parentEntityName;
		$this->parentParam = $parentParam;
		$this->childRepository = (string) $childRepositoryName;
		$this->childParam = $childParam;
	}

	/**
	 * Kontroluje asociace z druhe strany
	 * @param IRepositoryContainer
	 */
	public function check(IRepositoryContainer $model)
	{
		if (!$model->isRepository($this->childRepository))
		{
			throw new RelationshipLoaderException("{$this->childRepository} isn't repository in {$this->parentEntityName}::\${$this->parentParam}");
		}
	}

	/** @return string MetaData::ManyToOne|MetaData::OneToOne|MetaData::ManyToMany|MetaData::OneToMany */
	final public function getType()
	{
		return $this->type;
	}

	/** @return string */
	final public function getParentEntityName()
	{
		return $this->parentEntityName;
	}

	/** @return string */
	final public function getChildRepository()
	{
		return $this->childRepository;
	}

	/** @return string */
	final public function getChildParam()
	{
		return $this->childParam;
	}

	/** @return string */
	final public function getParentParam()
	{
		return $this->parentParam;
	}

	/**
	 * @param IRepositoryContainer
	 * @return string[]
	 */
	final public function getCanConnectWithEntities(IRepositoryContainer $model)
	{
		if (!$this->canConnectWith)
		{
			MetaData::getEntityRules($this->getParentEntityName(), $model); // zkontroluje integritu a naplni canConnectWith
			if (!$this->canConnectWith)
			{
				// znamena ze tento objekt neni v metadatech napr. ze se vytvoril rucne v testech
				// v beznem provozu by nemelo dochazat
				$this->check($model);
			}
		}
		return $this->canConnectWith;
	}

	/**
	 * @param IRepositoryContainer
	 * @param string MetaData::ManyToOne|MetaData::OneToOne|MetaData::ManyToMany|MetaData::OneToMany
	 * @param bool
	 * @param callback (RelationshipMetaData $parent, RelationshipMetaData $child)
	 */
	protected function checkIntegrity(IRepositoryContainer $model, $expectedType, $requiredChildParam, $callback = NULL)
	{
		$lowerEntityName = strtolower($this->parentEntityName);
		$classes = array_values((array) $model->getRepository($this->childRepository)->getEntityClassName());
		$this->canConnectWith = array_combine(array_map('strtolower', $classes), $classes);
		if (!$this->childParam) return;
		foreach ($this->canConnectWith as $lowerEn => $en)
		{
			$meta = MetaData::getEntityRules($en, $model, $this->childParam);
			$e = NULL;
			if (isset($meta[$this->childParam]))
			{
				$meta = $meta[$this->childParam];
				try {
					if ($meta['relationship'] !== $expectedType)
					{
						throw new RelationshipLoaderException("{$this->parentEntityName}::\${$this->parentParam} {{$this->type}} na druhe strane asociace {$en}::\${$this->childParam} neni asociace ktera by ukazovala zpet");
					}
					$loader = $meta['relationshipParam'];
					if ($requiredChildParam AND !$loader->childParam)
					{
						throw new RelationshipLoaderException("{$this->parentEntityName}::\${$this->parentParam} {{$this->type}} na druhe strane asociace {$en}::\${$this->childParam} neni vyplnen param ktery by ukazoval zpet");
					}
					if (!isset($loader->canConnectWith[$lowerEntityName]))
					{
						do {
							foreach ($loader->canConnectWith as $canConnectWithEntity)
							{
								if (is_subclass_of($canConnectWithEntity, $lowerEntityName))
								{
									break 2; // goto canConnect;
								}
							}
							throw new RelationshipLoaderException("{$this->parentEntityName}::\${$this->parentParam} {{$this->type}} na druhe strane asociace {$en}::\${$this->childParam} neukazuje zpet; ukazuje na jiny repository ({$loader->repository})");
						} while (false);
						// canConnect:
					}
					if ($loader->childParam AND $loader->childParam !== $this->parentParam)
					{
						throw new RelationshipLoaderException("{$this->parentEntityName}::\${$this->parentParam} {{$this->type}} na druhe strane asociace {$en}::\${$this->childParam} neukazuje zpet; ukazuje na jiny parametr ({$loader->childParam})");
					}
					if ($callback) call_user_func($callback, $this, $loader);
					continue;
				} catch (Exception $e) {}
			}
			unset($this->canConnectWith[$lowerEn]);
			if ($e) throw $e;
		}
		if (!$this->canConnectWith)
		{
			throw new RelationshipLoaderException("{$this->parentEntityName}::\${$this->parentParam} {{$this->type}} na druhe strane asociace {$this->repository}::\${$this->childParam} neni asociace ktera by ukazovala zpet");
		}
	}

	/**
	 * @deprecated
	 * @return string
	 */
	final public function getRepository()
	{
		return $this->getChildRepository();
	}

	/**
	 * @deprecated
	 * @return string
	 */
	final public function getParam()
	{
		return $this->getChildParam();
	}
}
