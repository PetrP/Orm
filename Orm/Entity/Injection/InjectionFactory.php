<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\Callback;

class InjectionFactory
{
	private $className;
	private $callback;

	private function __construct() {}

	public static function create(Callback $callback, $className)
	{
		$factory = new self;
		$factory->callback = $callback->getNative();
		$factory->className = $className;
		return callback($factory, 'call');
	}

	public function call(IEntity $entity, $value)
	{
		return call_user_func($this->callback, $this->className, $entity, $value);
	}

}
