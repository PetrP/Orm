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

	/**
	 * @param string MetaData::ManyToOne|MetaData::OneToOne|MetaData::ManyToMany|MetaData::OneToMany
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string|NULL
	 */
	public function __construct($type, $parentEntityName, $parentParam, $childRepositoryName, $childParam, $relationshipClass = NULL)
	{
		if ($type !== MetaData::OneToMany AND $type !== MetaData::ManyToMany) throw new InvalidArgumentException;
		parent::__construct($type, $parentEntityName, $parentParam, $childRepositoryName, $childParam);
		$this->setRelationshipClass($relationshipClass);
		if (!$childRepositoryName)
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
		return new $className($parent, $this, $value);
	}

	/** @return mixed RelationshipMetaDataToMany::MAPPED_* */
	abstract public function getWhereIsMapped();

	/** @param string */
	private function setRelationshipClass($relationshipClass)
	{
		$type = $this->getType();
		$mainClass = $type === MetaData::ManyToMany ? 'Orm\ManyToMany' : 'Orm\OneToMany';
		if (!$relationshipClass)
		{
			$relationshipClass = $mainClass;
		}
		else
		{
			if (!class_exists($relationshipClass))
			{
				throw new RelationshipLoaderException("{$this->parentEntityName}::\${$this->parentParam} {{$type}} excepts $mainClass class as type, class '$relationshipClass' doesn't exists");
			}
			$relationshipClass = ltrim($relationshipClass, '\\');
			if (!is_subclass_of($relationshipClass, $mainClass) AND strcasecmp($relationshipClass, $mainClass) !== 0)
			{
				throw new RelationshipLoaderException("{$this->parentEntityName}::\${$this->parentParam} {{$type}} Class '$relationshipClass' isn't instanceof $mainClass");
			}
		}
		$this->relationshipClass = $relationshipClass;
	}

}
