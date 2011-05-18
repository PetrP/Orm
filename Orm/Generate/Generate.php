<?php

namespace Orm;


class Generate extends Object
{
	public static function sql()
	{
		if (Environment::isProduction())
		{
			throw new Exception();
		}

		$generate = new GenerateTable(RepositoryContainer::get()); // todo di

		if (method_exists('Environment', 'getRobotLoader'))
		{
			$rl = Environment::getRobotLoader();
		}
		else
		{
			$rl = Environment::getService('Nette\Loaders\RobotLoader');
		}
		return $generate->getAllCreateTablesSql($rl);
	}

	public static function dump()
	{
		echo '<pre>';
		echo self::sql();
		exit;
	}

	public static function execute(DibiConnection $connection, $file = NULL)
	{
		$sql = array_filter(array_map('trim', explode(';', Generate::sql())));
		$count = count($sql);
		array_map(array($connection, 'nativeQuery'), $sql);
		if ($file !== NULL)
		{
			$files = func_get_args(); array_shift($files);
			$count += array_sum(array_map(array($connection, 'loadFile'), $files));
		}
		return $count;
	}

	public static function comment(Repository $repository)
	{
		$g = new GenerateEntityComment($repository->mapper);
		echo '<pre>';
		echo $g->getComment();
		exit;
	}

}
