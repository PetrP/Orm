<?php

use Orm\Entity;
use Orm\Repository;

/**
 * @property EventEntityFragment_Entity $one {m:1 EventEntityFragmentRepository}
 */
class EventEntityFragment_onAttachModel_Entity extends Entity
{

}
class EventEntityFragment_onAttachModel_Repository extends Repository
{
	protected $entityClassName = 'EventEntityFragment_onAttachModel_Entity';
}
class EventEntityFragment_onAttachModel_Mapper extends TestsMapper
{

}
