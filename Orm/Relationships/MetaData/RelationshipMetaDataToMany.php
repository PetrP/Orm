<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * MetaData for Orm\ManyToMany and Orm\OneToMany.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships\MetaData
 */
abstract class RelationshipMetaDataToMany extends RelationshipMetaData implements IEntityInjectionLoader
{

	/** Na teto strane */
	const MAPPED_HERE = true;

	/** Na druhe strane */
	const MAPPED_THERE = false;

	/** Relace ukazuje na sebe. Oba jsou stejny. Oba mapuji. */
	const MAPPED_BOTH = 2;

	/** @var string */
	private $relationshipClass;

	/** @var bool */
	protected $old = false;

	/**
	 * @param string MetaData::ManyToOne|MetaData::OneToOne|MetaData::ManyToMany|MetaData::OneToMany
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	public function __construct($type, $parentEntityName, $parentParam, $childRepositoryName, $childParam, $relationshipClass)
	{
		if ($type !== MetaData::OneToMany AND $type !== MetaData::ManyToMany) throw new InvalidArgumentException;
		parent::__construct($type, $parentEntityName, $parentParam, $childRepositoryName, $childParam);
		$this->setRelationshipClass($relationshipClass);
		if ($this->old AND $childRepositoryName)
		{
			$oldMainClass = $type === MetaData::ManyToMany ? 'Orm\OldManyToMany' : 'Orm\OldOneToMany';
			throw new RelationshipLoaderException("{$parentEntityName}::\${$parentParam} {{$type}} You can't specify foreign repository for $oldMainClass");
		}
		if (!$this->old AND !$childRepositoryName)
		{
			throw new RelationshipLoaderException("{$parentEntityName}::\${$parentParam} {{$type}} You must specify foreign repository {{$type} repositoryName param}");
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
		if ($this->relationshipClass !== $className) throw new RelationshipLoaderException;
		return new $className($parent, $this->repository, $this->param, $this->parentParam, $this->getWhereIsMapped(), $value);
	}

	/** @return mixed RelationshipMetaDataToMany::MAPPED_* */
	abstract public function getWhereIsMapped();

	/** @param string */
	private function setRelationshipClass($relationshipClass)
	{
		$type = $this->getType();
		$mainClass = $type === MetaData::ManyToMany ? 'Orm\ManyToMany' : 'Orm\OneToMany';
		if (!class_exists($relationshipClass))
		{
			throw new RelationshipLoaderException("{$this->parentEntityName}::\${$this->parentParam} {{$type}} excepts $mainClass class as type, class '$relationshipClass' doesn't exists");
		}
		$parents = class_parents($relationshipClass);
		if (strtolower($relationshipClass) !== strtolower($mainClass) AND !isset($parents[$mainClass]))
		{
			throw new RelationshipLoaderException("{$this->parentEntityName}::\${$this->parentParam} {{$type}} Class '$relationshipClass' isn't instanceof $mainClass");
		}
		$this->relationshipClass = $relationshipClass;
		if (isset($parents[$type === MetaData::ManyToMany ? 'Orm\OldManyToMany' : 'Orm\OldOneToMany']))
		{
			$this->old = true;
		}
	}

}
