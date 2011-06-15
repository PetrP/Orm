<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\Object;
use Nette\DeprecatedException;

require_once __DIR__ . '/IManyToManyMapper.php';
require_once __DIR__ . '/../Entity/Injection/IEntityInjection.php';

class ArrayManyToManyMapper extends Object implements IManyToManyMapper, IEntityInjection
{
	/** @var array id => id */
	private $value;

	/** @param array of id */
	public function setInjectedValue($value)
	{
		if (ValidationHelper::isValid(array('array'), $value) AND $value)
		{
			$this->value = array_combine($value, $value);
		}
		else
		{
			$this->value = array();
		}
	}

	/** @return array id => id */
	public function getInjectedValue()
	{
		return $this->value;
	}

	/** @param ManyToMany */
	public function attach(ManyToMany $manyToMany)
	{

	}

	/**
	 * @param IEntity
	 * @param array id => id
	 */
	public function add(IEntity $parent, array $ids)
	{
		$parent->isChanged(true);
		$this->value = $this->value + $ids;
	}

	/**
	 * @param IEntity
	 * @param array id => id
	 */
	public function remove(IEntity $parent, array $ids)
	{
		$parent->isChanged(true);
		$this->value = array_diff_key($this->value, $ids);
	}

	/**
	 * @param IEntity
	 * @return array id => id
	 */
	public function load(IEntity $parent)
	{
		return $this->value;
	}

	/** @deprecated */
	final public function setValue($value)
	{
		throw new DeprecatedException(get_class($this) . '::setValue() is deprecated; use ' . get_class($this) . '::setInjectedValue() instead');
	}

	/** @deprecated */
	final public function getValue()
	{
		throw new DeprecatedException(get_class($this) . '::getValue() is deprecated; use ' . get_class($this) . '::getInjectedValue() instead');
	}

	/** @deprecated */
	final public function setParams($parentIsFirst)
	{
		throw new DeprecatedException;
	}

}
