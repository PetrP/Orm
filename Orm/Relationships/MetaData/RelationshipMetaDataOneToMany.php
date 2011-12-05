<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * MetaData for Orm\OneToMany.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships\MetaData
 */
class RelationshipMetaDataOneToMany extends RelationshipMetaDataToMany
{

	/**
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string|NULL
	 */
	public function __construct($parentEntityName, $parentParam, $childRepositoryName, $childParam, $relationshipClass = NULL)
	{
		if (!$childParam)
		{
			$childParam = $parentEntityName;
			if ($childParam{0} != '_') $childParam{0} = $childParam{0} | "\x20"; // lcfirst
		}
		parent::__construct(MetaData::OneToMany, $parentEntityName, $parentParam, $childRepositoryName, $childParam, $relationshipClass);
	}

	/**
	 * Kontroluje asociace z druhe strany
	 * @param IRepositoryContainer
	 */
	public function check(IRepositoryContainer $model)
	{
		parent::check($model);

		$this->checkIntegrity($model, MetaData::ManyToOne, false);
	}

	/** @return mixed RelationshipMetaDataToMany::MAPPED_* */
	final public function getWhereIsMapped()
	{
		return self::MAPPED_THERE;
	}
}
