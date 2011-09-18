<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Factory for IEntityInjection.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\Injection
 */
class InjectionFactory
{
	private $className;
	private $callback;

	/**
	 * @todo remove
	 */
	private function __construct()
	{
	}

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
		return Callback::create($factory, 'call');
	}

	/**
	 * @param IEntity
	 * @param mixed
	 * @return IEntityInjection
	 */
	public function call(IEntity $entity, $value)
	{
		$result = call_user_func($this->callback, $this->className, $entity, $value);
		if (!($result instanceof IEntityInjection))
		{
			$tmp = array(NULL, $this->className . ' factory');
			if (is_array($this->callback) AND count($this->callback) === 2)
			{
				$tmp = $this->callback;
			}
			throw new BadReturnException(array($tmp[0], $tmp[1], 'Orm\IEntityInjection', $result));
		}
		return $result;
	}

}
