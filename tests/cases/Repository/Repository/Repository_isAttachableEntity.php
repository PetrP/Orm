<?php

use Orm\Repository;

class Repository_isAttachableEntity_Repository extends Repository
{
	public function getEntityClassName(array $data = NULL)
	{
		static $classes = array('TestEntity1', 'TestEntity2', 'TestEntity3');
		if ($data === NULL) return $classes;
		return $classes[$data['type']];
	}
}

class TestEntity1 extends TestEntity {}
class TestEntity2 extends TestEntity {}
class TestEntity3 extends TestEntity {}
