<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use stdClass;

/**
 * Gets join info.
 * @see DibiMapper::getJoinInfo()
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Helpers
 */
class DibiJoinHelper extends Object
{
	/** @var DibiMapper */
	private $mapper;

	/** @var array $sourceKey => array @see self::getInfo() */
	private $cache = array();

	/** @var array name => array */
	private $relationships = array();

	/** @param DibiMapper */
	public function __construct(DibiMapper $mapper)
	{
		$this->mapper = $mapper;

		$model = $mapper->getModel();
		$repository = $mapper->getRepository();
		foreach ((array) $repository->getEntityClassName() as $entityName)
		{
			foreach (MetaData::getEntityRules($entityName, $model) as $name => $rule)
			{
				if ($rule['relationship'] === MetaData::OneToMany)
				{
					$loader = $rule['relationshipParam'];
					if ($r = $loader->getRepository() AND $p = $loader->getParam())
					{
						$this->relationships[$name] = array($r, array('id', false), array($p, false));
					}
				}
				else if ($rule['relationship'] === MetaData::ManyToMany)
				{
					$loader = $rule['relationshipParam'];
					if ($r = $loader->getRepository() AND $childParam = $loader->getParam())
					{
						$parentRepository = $repository;
						$childRepository = $model->getRepository($r);
						$parentParam = $loader->getParentParam();
						$mapped = $loader->getWhereIsMapped();
						if ($mapped === RelationshipLoader::MAPPED_HERE OR $mapped === RelationshipLoader::MAPPED_BOTH)
						{
							$manyToManyMapper = $this->mapper->createManyToManyMapper($parentParam, $childRepository, $childParam);
							$parentParam = $manyToManyMapper->parentParam;
							$childParam = $manyToManyMapper->childParam;
						}
						else
						{
							$manyToManyMapper = $childRepository->getMapper()->createManyToManyMapper($childParam, $parentRepository, $parentParam);
							$parentParam = $manyToManyMapper->childParam;
							$childParam = $manyToManyMapper->parentParam;
						}
						$this->relationships[$name] = array($r, array($childParam, true), array('id', false), array($manyToManyMapper->table, array('id', false), array($parentParam, true)));
					}
				}
				else if ($rule['relationship'] === MetaData::ManyToOne OR $rule['relationship'] === MetaData::OneToOne)
				{
					$this->relationships[$name] = array($rule['relationshipParam'], array(NULL, false), array('id', false));
				}
			}
		}
	}

	/**
	 * Get join info.
	 * Work with all propel defined association.
	 * @param string author->lastName or author->group->name
	 * @param stdClass|NULL previos info
	 * @return object
	 * 	->key author.last_name
	 *  ->joins[] array(
	 * 		alias => author
	 * 	?	sourceKey => author
	 * 		xConventionalKey => author_id
	 * 		yConventionalKey => id
	 * 		table => users
	 * 		findBy => array
	 * 	?	mapper => DibiMapper
	 * 	?	conventional => IDatabaseConventional
	 * )
	 */
	public function get($key, stdClass $result = NULL)
	{
		if (!$result)
		{
			$result = (object) array('key' => NULL, 'joins' => array());
		}
		$lastJoin = end($result->joins);

		$tmp = explode('->', $key, 3);
		$sourceKey = $tmp[0];
		$targetKey = $tmp[1];
		$next = isset($tmp[2]) ? $tmp[2] : NULL;

		foreach ($this->getInfo($sourceKey) as $join)
		{
			if ($lastJoin)
			{
				$join['alias'] = $lastJoin['alias'] . '->' . $join['alias'];
			}
			$result->joins[] = $join;
		}

		if ($next)
		{
			$result = $join['mapper']->getJoinInfo($targetKey . '->' . $next, $result);
		}
		else
		{
			$tmp = $join['conventional']->formatEntityToStorage(array($targetKey => NULL));
			$targetConventionalKey = key($tmp);
			$result->key = $join['alias'] . '.' . $targetConventionalKey;
		}
		return $result;
	}

	/**
	 * @param string
	 * @return array
	 */
	private function getInfo($sourceKey)
	{
		if (!isset($this->cache[$sourceKey]))
		{
			$tmp = array();
			$mappper = $this->mapper;
			$conventional = $this->mapper->getConventional();
			$model = $this->mapper->getModel();
			if (!isset($this->relationships[$sourceKey]))
			{
				throw new MapperJoinException(get_class($this->mapper->getRepository()) . ": neni zadna vazba na `$sourceKey`");
			}

			$joinRepository = $model->getRepository($this->relationships[$sourceKey][0]);
			$tmp['mapper'] = $joinRepository->getMapper();
			if (!($tmp['mapper'] instanceof DibiMapper))
			{
				throw new MapperJoinException(get_class($joinRepository) . " ($sourceKey) nepouziva Orm\\DibiMapper, data nelze propojit.");
			}
			$tmp['conventional'] = $tmp['mapper']->getConventional();

			$manyToManyJoin = NULL;
			if (isset($this->relationships[$sourceKey][3]))
			{
				$manyToManyJoin = $this->relationships[$sourceKey][3];
				$manyToManyJoin = array(
					'alias' => 'm2m__' . $sourceKey,
					'xConventionalKey' => $this->format($manyToManyJoin[1], $conventional),
					'yConventionalKey' => $this->format($manyToManyJoin[2], $tmp['conventional']),
					'table' => $manyToManyJoin[0],
					'findBy' => array(),
				);
			}

			$tmp['table'] = NULL; // lazy load v DibiMapper::getJoinInfo() ($tmp['mapper']->getTableName())
			// todo brat table z collection?
			if ($tmp['mapper']->getConnection() !== $this->mapper->getConnection())
			{
				// todo porovnavat connection na collection?
				throw new MapperJoinException(get_class($joinRepository) . " ($sourceKey) pouziva jiny Orm\\DibiConnection nez " . get_class($this->mapper->getRepository()) . ", data nelze propojit.");
			}
			$tmp['sourceKey'] = $sourceKey;
			$tmp['xConventionalKey'] = $this->format($this->relationships[$sourceKey][1], $conventional, $sourceKey);
			$tmp['yConventionalKey'] = $this->format($this->relationships[$sourceKey][2], $tmp['conventional']);
			$tmp['alias'] = $sourceKey;

			$collection = $tmp['mapper']->findAll();
			if (!($collection instanceof DibiCollection))
			{
				throw new MapperJoinException(get_class($joinRepository) . " ($sourceKey) nepouziva Orm\\DibiCollection, data nelze propojit.");
			}
			$collectionArray = (array) $collection; // hack
			if ($collectionArray["\0*\0where"])
			{
				throw new MapperJoinException(get_class($joinRepository) . " ($sourceKey) Orm\\DibiCollection pouziva where(), data nelze propojit.");
			}
			$tmp['findBy'] = $collectionArray["\0*\0findBy"];

			$this->cache[$sourceKey] = $manyToManyJoin ? array($manyToManyJoin, $tmp) : array($tmp);
		}
		return $this->cache[$sourceKey];
	}

	/**
	 * @param array(string $key, bool $wasFormated)
	 * @param IDatabaseConventional
	 * @param string|NULL
	 * @return string
	 */
	private function format(array $key, IDatabaseConventional $conventional, $default = NULL)
	{
		list($key, $wasFormated) = $key;
		if ($key === NULL)
		{
			$key = $default;
		}
		if (!$wasFormated)
		{
			$tmp = $conventional->formatEntityToStorage(array($key => NULL));
			$key = key($tmp);
		}
		return $key;
	}

}
