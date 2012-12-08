<?php

use Orm\Repository;
use Orm\Entity;
use Orm\Inflector;


/** @mapper TestMapper */
class RelationshipMetaData_nesting_level_optimalization_ARepository extends Repository
{
	static $num = 100;
	private $getEntityClassName;
	public function getEntityClassName(array $data = NULL)
	{
		if ($this->getEntityClassName === NULL)
		{
			$helper = $this->getModel()->getContext()->getService('repositoryHelper', 'Orm\RepositoryHelper');
			$e = Inflector::singularize($helper->normalizeRepository($this));
			$r = array();
			foreach (range(1, self::$num) as $i)
			{
				$r[] = $e . '_' . $i;
			}
			$this->getEntityClassName = $r;
		}
		if ($data === NULL)
		{
			return $this->getEntityClassName;
		}
		return current($this->getEntityClassName);
	}
}

/** @mapper TestMapper */
class RelationshipMetaData_nesting_level_optimalization_BRepository extends RelationshipMetaData_nesting_level_optimalization_ARepository
{
}

foreach (range(1, RelationshipMetaData_nesting_level_optimalization_ARepository::$num) as $i)
{
eval('
/**
 * @property RelationshipMetaData_nesting_level_optimalization_B_'.$i.' $e {1:1 RelationshipMetaData_nesting_level_optimalization_BRepository $e}
 */
class RelationshipMetaData_nesting_level_optimalization_A_'.$i.' extends Orm\Entity {}
');
eval('
/**
 * @property RelationshipMetaData_nesting_level_optimalization_A_'.$i.' $e {1:1 RelationshipMetaData_nesting_level_optimalization_ARepository $e}
 */
class RelationshipMetaData_nesting_level_optimalization_B_'.$i.' extends Orm\Entity {}
');
}



foreach (range(1, $max = 100) as $i)
{
$prev = $i === 1 ? $max : $i-1;
$next = $i === $max ? 1 : $i+1;
eval('
/** @mapper TestMapper */
class RelationshipMetaData_nesting_level_optimalization_X_'.$i.'Repository extends Orm\Repository
{}
');
eval('
/**
 * @property RelationshipMetaData_nesting_level_optimalization_X_'.$prev.' $prev {1:1 RelationshipMetaData_nesting_level_optimalization_X_'.$prev.'Repository $next}
 * @property RelationshipMetaData_nesting_level_optimalization_X_'.$next.' $next {1:1 RelationshipMetaData_nesting_level_optimalization_X_'.$next.'Repository $prev}
 */
class RelationshipMetaData_nesting_level_optimalization_X_'.$i.' extends Orm\Entity {}
');
}
