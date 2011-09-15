<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\Callback;

/**
 * Factory for IEntityInjection.
 */
class InjectionFactory
{
	private $className;
	private $callback;

	private function __construct() {}

	/**
	 * @param Callback
	 * @param string
	 * @return Callback
	 */
	public static function create(Callback $callback, $className)
	{
		$factory = new self;
		$factory->callback = $callback->getNative();
		$factory->className = $className;
		return callback($factory, 'call');
	}

	/**
	 * @param IEntity
	 * @param mixed
	 * @return IEntityInjection
	 */
	public function call(IEntity $entity, $value)
	{
		return call_user_func($this->callback, $this->className, $entity, $value);
	}

}
