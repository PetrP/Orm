<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use ReflectionClass;
use Exception;

/**
 * Helper for methods that can be automatically called from repository to mapper.
 * @see Repository::__call()
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Helpers
 */
class MapperAutoCaller extends Object
{

	/** @var array name => true */
	private $methods = array();

	/**
	 * @param IRepository
	 * @param AnnotationsParser
	 */
	public function __construct(IRepository $repository, AnnotationsParser $parser)
	{
		$reflections = array($reflection = new ReflectionClass($repository));
		while ($reflection = $reflection->getParentClass() AND $reflection->implementsInterface('Orm\IRepository'))
		{
			$reflections[] = $reflection;
		}
		$repoMethods = array_fill_keys(array_map('strtolower', get_class_methods($repository)), true);
		foreach (array_reverse($reflections) as $reflection)
		{
			$annotation = $parser->getByReflection($reflection);
			if (isset($annotation['method']))
			{
				foreach ($annotation['method'] as $method)
				{
					if (preg_match('#^\s*(?:[^\s\(]+\s+)?([^\s\(]+)(?:\(|\s|$)#si', $method, $match))
					{
						$method = $match[1];
						$lcMethod = strtolower($method);
						if (isset($this->methods[$lcMethod]) OR isset($repoMethods[$lcMethod]))
						{
							$class = $reflection->getName();
							$repoClass = get_class($repository);
							$tmp = isset($this->methods[$lcMethod]) ? 'annotation' : 'method';
							throw new MapperAutoCallerException("$class::@method cannot redeclare $repoClass::$method(); $tmp already exists.");
						}
						$this->methods[$lcMethod] = $this->methods[$method] = true;
					}
					else
					{
						$class = $reflection->getName();
						throw new MapperAutoCallerException("$class::@method invalid format; '$method' given.");
					}
				}
			}
		}
	}

	/**
	 * Has method?
	 * @param string
	 * @return bool
	 */
	public function has($name)
	{
		if (isset($this->methods[$name]))
		{
			return true;
		}
		$lname = strtolower($name);
		if (isset($this->methods[$lname]))
		{
			$this->methods[$name] = true;
			return true;
		}
		return false;
	}

}
