<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Reflector;

/**
 * Wrapper over annotations parser.
 * It can use custom callback or auto detect Nette\Reflection\AnnotationsParser.
 *
 * Custom callback can be registered like this:
 * <code>
 * $container = $repositoryContainer->getContext();
 * $container->addService('annotationsParser', function () {
 * 	return new AnnotationsParser(array('MyCustomAnnotationsParser', 'getAll'));
 * });
 *
 * abstract class BaseEntity extends Orm\Entity
 * {
 * 	public static function createMetaData($entityClass)
 * 	{
 * 		$parser = new AnnotationsParser(array('MyCustomAnnotationsParser', 'getAll'));
 * 		return AnnotationMetaData::getMetaData($entityClass, $parser);
 * 	}
 * }
 * </code>
 *
 * @see AnnotationMetaData
 * @see AnnotationClassParser
 * @author Petr Procházka
 * @package Orm
 * @subpackage Common
 */
class AnnotationsParser extends Object
{
	/** @var mixed */
	private $callback;

	/** @param callback|NULL null means autodetect nette  */
	public function __construct($callback = NULL)
	{
		if ($callback === NULL)
		{
			static $detect;
			if ($detect === NULL)
			{
				foreach (array(
					'Nette\Reflection\AnnotationsParser', 'NAnnotationsParser', 'AnnotationsParser',
					'Nette\Annotations', 'NAnnotations', 'Annotations',
				) as $class)
				{
					if (class_exists($class) AND method_exists($class, 'getAll'))
					{
						$detect = array($class, 'getAll');
						break;
					}
				}
				if ($detect === NULL)
				{	// @codeCoverageIgnoreStart
					throw new NotSupportedException('Nette\Reflection\AnnotationsParser is not available; You can implement custom parser.');
				}	// @codeCoverageIgnoreEnd
			}
			$callback = $detect;
		}
		$this->callback = $callback;
	}

	/**
	 * Returns annotations.
	 * @param  ReflectionClass|ReflectionMethod|ReflectionProperty
	 * @return array
	 * @see Nette\Reflection\AnnotationsParser::getAll()
	 */
	public function getByReflection(Reflector $r)
	{
		return call_user_func($this->callback, $r);
	}
}
