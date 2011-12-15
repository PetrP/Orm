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
class ArrayManyToManyMapper extends Object implements IManyToManyMapper
{

	/** @var RelationshipMetaDataManyToMany */
	private $meta;

	/**
	 * @param array id => id {@see ManyToMany::$injectedValue}
	 * @return array id => id will be set as {@see ManyToMany::$injectedValue}
	 */
	public function validateInjectedValue($injectedValue)
	{
		if (ValidationHelper::isValid(array('array'), $injectedValue) AND $injectedValue)
		{
			$injectedValue = array_combine($injectedValue, $injectedValue);
		}
		else
		{
			$injectedValue = array();
		}
		return $injectedValue;
	}

	/** @param RelationshipMetaDataManyToMany */
	public function attach(RelationshipMetaDataManyToMany $meta)
	{
		$this->meta = $meta;
		$mapped = $meta->getWhereIsMapped();
		if ($mapped === RelationshipMetaDataToMany::MAPPED_THERE)
		{
			throw new NotSupportedException('Orm\ArrayManyToManyMapper has support only on side where is relationship mapped.');
		}
		if ($mapped === RelationshipMetaDataToMany::MAPPED_BOTH)
		{
			throw new NotSupportedException('Orm\ArrayManyToManyMapper not support relationship to self.');
		}
	}

	/**
	 * @param IEntity
	 * @param array id => id
	 * @param array id => id {@see ManyToMany::$injectedValue}
	 * @return array id => id will be set as {@see ManyToMany::$injectedValue}
	 */
	public function add(IEntity $parent, array $ids, $injectedValue)
	{
		if ($this->meta->getWhereIsMapped() === RelationshipMetaDataToMany::MAPPED_THERE)
		{
			throw new NotSupportedException('Orm\IManyToManyMapper::add() has not supported on inverse side.');
		}

		$parent->markAsChanged($this->meta->getParentParam());
		$injectedValue = $injectedValue + $ids;
		return $injectedValue;
	}

	/**
	 * @param IEntity
	 * @param array id => id
	 * @param array id => id {@see ManyToMany::$injectedValue}
	 * @return array id => id will be set as {@see ManyToMany::$injectedValue}
	 */
	public function remove(IEntity $parent, array $ids, $injectedValue)
	{
		if ($this->meta->getWhereIsMapped() === RelationshipMetaDataToMany::MAPPED_THERE)
		{
			throw new NotSupportedException('Orm\IManyToManyMapper::remove() has not supported on inverse side.');
		}

		$parent->markAsChanged($this->meta->getParentParam());
		$injectedValue = array_diff_key($injectedValue, $ids);
		return $injectedValue;
	}

	/**
	 * @param IEntity
	 * @param array id => id
	 * @param array id => id {@see ManyToMany::$injectedValue}
	 * @return array id => id
	 */
	public function load(IEntity $parent, $injectedValue)
	{
		return $injectedValue;
	}

	/** @deprecated */
	final public function setInjectedValue($value)
	{
		throw new DeprecatedException(array($this, 'setInjectedValue()', $this, 'validateInjectedValue()'));
	}

	/** @deprecated */
	final public function getInjectedValue()
	{
		throw new DeprecatedException(array($this, 'getInjectedValue()', $this, 'validateInjectedValue()'));
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
