<?php

use Orm\Entity;
use Orm\Repository;

/**
 * @property RelationshipMetaDataOneToOne_OneToOneParent_Entity $oneParent {1:1 RelationshipMetaDataOneToOne_OneToOne_Repository $oneParent}
 */
abstract class RelationshipMetaDataOneToOne_OneToOneParent_Entity extends Entity
{

}

/**
 * @property RelationshipMetaDataOneToOne_OneToOne_Entity $one {1:1 RelationshipMetaDataOneToOne_OneToOne_Repository $one}
 */
class RelationshipMetaDataOneToOne_OneToOne_Entity extends RelationshipMetaDataOneToOne_OneToOneParent_Entity
{

}

/**
 * @mapper TestsMapper
 */
class RelationshipMetaDataOneToOne_OneToOne_Repository extends Repository
{
	protected $entityClassName = 'RelationshipMetaDataOneToOne_OneToOne_Entity';
}
