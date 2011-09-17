<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * DI Container.
 * @author Petr Procházka
 * @package Orm
 * @subpackage DI
 */
interface IServiceContainer
{

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
	 */
	public function addService($name, $service);

	/**
	 * @param string
	 * @param string|NULL
	 * @return object
	 * @throws ServiceNotFoundException
	 * @throws InvalidServiceFactoryException
	 * @throws ServiceNotInstanceOfException if $instanceof not match with service
	 */
	public function getService($name, $instanceof = NULL);

	/**
	 * @param string
	 * @return object
	 * @throws ServiceNotFoundException
	 */
	public function removeService($name);

	/**
	 * @param string
	 * @param bool
	 * @return bool
	 * @throws ServiceNotFoundException if $throw is true
	 */
	public function hasService($name, $throw = false);

	/**
	 * Makes the object unmodifiable.
	 * @return ServiceNotFoundException
	 */
	public function freeze();

}
