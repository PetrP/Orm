<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Reflector;

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
	public function getByReflection(\Reflector $r)
	{
		return call_user_func($this->callback, $r);
	}
}
