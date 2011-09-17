<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Closure;

/**
 * DI Container.
 * @author Petr Procházka
 * @package Orm
 * @subpackage DI
 */
class ServiceContainer extends Object implements IServiceContainer
{

	/** @var array of name => Object */
	private $services = array();

	/** @var bool */
	private $frozen = false;

	/**
	 * <code>
	 * 	$c->addService('foo', function (Orm\IServiceContainer $c) { return new Foo; });
	 * 	$c->addService('foo', 'Foo');
	 * 	$c->addService('foo', new Foo);
	 * </code>
	 *
	 * @param string
	 * @param Callback|Closure|string|Object class name, callback or object
	 * @return IServiceContainer
	 * @throws ServiceAlreadyExistsException
	 * @throws FrozenContainerException
	 */
	public function addService($name, $service)
	{
		$this->updating();
		if (isset($this->services[$name]))
		{
			throw new ServiceAlreadyExistsException("Service '$name' already exists");
		}
		$tmp = (object) array(
			'service' => NULL,
			'factory' => $service,
		);
		$this->services[$name] = $tmp;
		return $this;
	}

	/**
	 * @param string
	 * @param string|NULL
	 * @return object
	 * @throws ServiceNotFoundException
	 * @throws InvalidServiceFactoryException
	 * @throws ServiceNotInstanceOfException if $instanceof not match with service
	 */
	public function getService($name, $instanceof = NULL)
	{
		$this->hasService($name, true);
		$s = $this->services[$name];
		if ($s->service === NULL)
		{

			if (is_string($s->factory) AND !(strpos($s->factory, '::') OR strncmp($s->factory, "\0lambda_", 8) === 0))
			{
				$s->service = new $s->factory;
				unset($s->factory);
			}
			else if (Callback::is($s->factory) OR $s->factory instanceof Closure OR is_string($s->factory) OR is_array($s->factory))
			{
				$tmp = Callback::create($s->factory)->invoke($this);
				if (!is_object($tmp))
				{
					$tmp = gettype($tmp);
					throw new InvalidServiceFactoryException("Factory for service '$name' returns invalid result. Object expected, $tmp given.");
				}
				$s->service = $tmp;
				unset($s->factory);
			}
			else if (is_object($s->factory))
			{
				$s->service = $s->factory;
				unset($s->factory);
			}
			else
			{
				$tmp = gettype($s->factory);
				throw new InvalidServiceFactoryException("Service '$name' has invalid factory. Callback, class name or object expected, $tmp given.");
			}
		}
		if ($instanceof !== NULL AND !($s->service instanceof $instanceof))
		{
			throw new ServiceNotInstanceOfException("Service '$name' is not instance of '$instanceof'.");
		}
		return $s->service;
	}

	/**
	 * @param string
	 * @return object
	 * @throws ServiceNotFoundException
	 * @throws FrozenContainerException
	 */
	public function removeService($name)
	{
		$this->updating();
		$this->hasService($name, true);
		unset($this->services[$name]);
		return $this;
	}

	/**
	 * @param string
	 * @param bool
	 * @return bool
	 * @throws ServiceNotFoundException if $throw is true
	 */
	public function hasService($name, $throw = false)
	{
		if (!isset($this->services[$name]))
		{
			if ($throw)
			{
				throw new ServiceNotFoundException("Service '$name' not found");
			}
			return false;
		}
		return true;
	}

	/**
	 * Makes the object unmodifiable.
	 * @return ServiceNotFoundException
	 */
	public function freeze()
	{
		$this->frozen = true;
		return $this;
	}

	/**
	 * @return void
	 * @throws FrozenContainerException
	 */
	protected function updating()
	{
		if ($this->frozen)
		{
			throw new FrozenContainerException("Cannot modify a frozen container.");
		}
	}
}
