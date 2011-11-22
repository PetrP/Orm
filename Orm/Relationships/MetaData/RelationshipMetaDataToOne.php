<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * MetaData for Orm\ManyToOne and Orm\OneToOne.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships\MetaData
 */
abstract class RelationshipMetaDataToOne extends RelationshipMetaData
{

	/**
	 * @param string MetaData::ManyToOne|MetaData::OneToOne|MetaData::ManyToMany|MetaData::OneToMany
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	public function __construct($type, $parentEntityName, $parentParam, $childRepositoryName, $childParam)
	{
		if ($type !== MetaData::ManyToOne AND $type !== MetaData::OneToOne) throw new InvalidArgumentException;
		if (!$childRepositoryName)
		{
			throw new RelationshipLoaderException("{$parentEntityName}::\${$parentParam} {{$type}} You must specify foreign repository {{$type} repositoryName}");
		}
		parent::__construct($type, $parentEntityName, $parentParam, $childRepositoryName, $childParam);
	}

	/** @return string repositoryName for BC */
	public function __toString()
	{
		return $this->getChildRepository();
	}

}
