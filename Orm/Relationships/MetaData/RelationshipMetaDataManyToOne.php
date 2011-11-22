<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * MetaData for Orm\ManyToOne.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships\MetaData
 */
class RelationshipMetaDataManyToOne extends RelationshipMetaDataToOne
{

	/**
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	public function __construct($parentEntityName, $parentParam, $childRepositoryName, $childParam)
	{
		parent::__construct(MetaData::ManyToOne, $parentEntityName, $parentParam, $childRepositoryName, $childParam);
	}

}
