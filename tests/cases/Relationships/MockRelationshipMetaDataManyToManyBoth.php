<?php

use Orm\RelationshipMetaDataManyToMany;

class MockRelationshipMetaDataManyToManyBoth extends RelationshipMetaDataManyToMany
{
	public function __construct($parentEntityName, $parentParam, $childRepositoryName, $childParam, $relationshipClass = NULL, $mapped = NULL)
	{
		parent::__construct($parentEntityName, $parentParam, $childRepositoryName, $childParam, $relationshipClass, $mapped);
		$this->checkIntegrityCallback($this, $this);
	}
}
