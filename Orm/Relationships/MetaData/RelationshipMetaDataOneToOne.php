<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * MetaData for Orm\OneToOne.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships\MetaData
 */
class RelationshipMetaDataOneToOne extends RelationshipMetaDataToOne
{

	/**
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	public function __construct($parentEntityName, $parentParam, $childRepositoryName, $childParam)
	{
		parent::__construct(MetaData::OneToOne, $parentEntityName, $parentParam, $childRepositoryName, $childParam);
	}

}
