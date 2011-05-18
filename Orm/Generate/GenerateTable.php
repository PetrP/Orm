<?php

namespace Orm;

use Nette\Object;
use Nette\Loaders\RobotLoader;
use DibiConnection;

require_once dirname(__FILE__) . '/GenerateDriver.php';
require_once dirname(__FILE__) . '/GenerateDriverMysql.php';

class GenerateTable extends Object
{
	private $model;

	public function __construct(RepositoryContainer $model)
	{
		$this->model = $model;
	}

	private function getRules(Repository $repository)
	{
		$entities = (array) $repository->getEntityClassName(NULL);
		$properties = array();
		foreach ($entities as $entityName)
		{
			$properties = array_merge($properties, MetaData::getEntityRules($entityName));
		}
		return $properties;
	}

	private function getTableName(DibiMapper $mapper)
	{
		if (!class_exists('MockDibiMapper__')) eval('
			class MockDibiMapper__ extends DibiMapper
			{
				static function gtn(DibiMapper $mapper) {return $mapper->getTableName();}
			}
		');
		return MockDibiMapper__::gtn($mapper);
	}

	protected function createDriver(DibiConnection $connection, $tableName)
	{
		$driverName = GenerateDriver::getDriverClassName($connection);
		return new $driverName($connection, $tableName);
	}

	public function getCreateTableSql(Repository $repository)
	{
		/** @var SqlConventional */
		$conventional = $repository->mapper->conventional;
		$properties = $conventional->formatEntityToStorage($this->getRules($repository));

		$driver = $this->createDriver($repository->mapper->connection, $this->getTableName($repository->mapper));

		foreach ($properties as $name => $rule)
		{
			$rule = (object) $rule;
			if ($name === 'id')
				$driver->addPrimary($name);
			else
			{
				$null = isset($rule->types['null']);
				if (in_array('string', $rule->types))
					$driver->addString($name, $null);
				else if (isset($rule->types['int']))
					$driver->addInt($name, $null);
				else if (isset($rule->types['float']))
					$driver->addFloat($name, $null);
				else if (isset($rule->types['bool']))
					$driver->addBool($name, $null);
				else if (isset($rule->types['datetime']))
					$driver->addDatetime($name, $null);
				else if (isset($rule->types['array']))
					$driver->addArray($name, $null);
				else if ($rule->relationship === MetaData::OneToOne OR $rule->relationship === MetaData::ManyToOne)
					$driver->addForeignKey($name, $null);
				else if ($rule->relationship === MetaData::OneToMany OR $rule->relationship === MetaData::ManyToMany)
					continue;
				else $driver->addMixed($name, $null);
			}
		}

		return $driver->getSql();
	}

	public function getAllCreateTablesSql(RobotLoader $robotLoader)
	{
		$result = array();
		$robotLoader->rebuild();
		foreach ($robotLoader->getIndexedClasses() as $class => $foo)
		{
			if (preg_match('#^(.+)Repository$#si', $class, $match))
			{
				$repoName = $match[1];
				if ($this->model->isRepository($repoName))
				{
					$repo = $this->model->getRepository($repoName);
					if ($repo->getMapper() instanceof DibiMapper)
					{
						$result[] = $this->getCreateTableSql($repo);
					}
				}
			}
		}
		return "\n".implode("\n", $result)."\n";
	}


}
