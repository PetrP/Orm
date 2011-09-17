<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Makes possible to wrap value in entity.
 *
 * <code>
 * /**
 *  * @property UrlInjection $url {injection}
 *  * /
 * class Foo extends Entity
 *
 * class UrlInjection extends Nette\Http\Url implements IEntityInjection, IEntityInjectionStaticLoader
 * {
 * 	public function getInjectedValue()
 * 	{
 * 		return $this->getAbsoluteUrl();
 * 	}
 *
 * 	public function setInjectedValue($value)
 * 	{
 * 		$this->__construct($value); // little hack
 * 	}
 *
 * 	public static function create($className, IEntity $entity, $value = NULL)
 * 	{
 * 		return new UrlInjection($value);
 * 	}
 * }
 *
 * $foo = new Foo;
 * $foo->url = 'http://foo.bar';
 * $foo->url; // instanceof UrlInjection
 * $foo->url->getScheme(); // === http
 *
 * </code>
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\Injection
 */
interface IEntityInjection
{

	/**
	 * Hodnota ktera se bude ukladat do uloziste.
	 * @return mixed
	 */
	function getInjectedValue();

	/**
	 * To co prijde od uzivatele.
	 * @param mixed
	 */
	function setInjectedValue($value);

}
