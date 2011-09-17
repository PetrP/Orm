<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Mapper for ManyToMany relationship.
 * It saves array of id at parent entity.
 *
 * @see IMapper::createManyToManyMapper()
 * @see ArrayMapper::createManyToManyMapper()
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships\Mappers
 */
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
		if ($manyToMany->getWhereIsMapped() === RelationshipLoader::MAPPED_THERE)
		{
			throw new NotSupportedException('Orm\ArrayManyToManyMapper has support only on side where is realtionship mapped.');
		}
		if ($manyToMany->getWhereIsMapped() === RelationshipLoader::MAPPED_BOTH)
		{
			throw new NotSupportedException('Orm\ArrayManyToManyMapper not support relationship to self.');
		}
	}

	/**
	 * @param IEntity
	 * @param array id => id
	 */
	public function add(IEntity $parent, array $ids)
	{
		$parent->markAsChanged();
		$this->value = $this->value + $ids;
	}

	/**
	 * @param IEntity
	 * @param array id => id
	 */
	public function remove(IEntity $parent, array $ids)
	{
		$parent->markAsChanged();
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
		throw new DeprecatedException(array($this, 'setValue()', $this, 'setInjectedValue()'));
	}

	/** @deprecated */
	final public function getValue()
	{
		throw new DeprecatedException(array($this, 'getValue()', $this, 'getInjectedValue()'));
	}

	/** @deprecated */
	final public function setParams($parentIsFirst)
	{
		throw new DeprecatedException(array($this, 'setParams()', $this, 'attach()'));
	}

}
