<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Exception;

/**
 * @deprecated
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships
 */
abstract class OldOneToMany extends OneToMany
{

	/** @var string get_class */
	private $name;

	private $parent;

	/**
	 * @param IEntity
	 */
	public function __construct(IEntity $parent, $foo1, $foo2, $parentParam)
	{
		$this->name = get_class($this);
		if (!strpos($this->name, 'To')) throw new Exception(); // todo
		$entityName = $this->getFirstEntityName();
		if (!($parent instanceof $entityName))
		{
			throw new InvalidEntityException($this->name . " expected '$entityName' as parent, " . get_class($parent) . ' given.');
		}
		$this->parent = $parent;
		parent::__construct($parent, $this->getSecondRepository(), $this->getSecondParamName(), $parentParam);
	}

	/**
	 * Nazev entity s kterou na kterou se pripojuje.
	 * @return string
	 */
	protected function getFirstEntityName()
	{
		return substr($this->name, 0, strpos($this->name, 'To'));
	}

	/**
	 * Nazev parametru na pripojenych entitach.
	 * @return string
	 */
	protected function getSecondParamName()
	{
		$param =  $this->getFirstEntityName();;
		if ($param{0} != '_') $param{0} = $param{0} | "\x20";
		return $param;
	}

	/**
	 * Repository
	 * @return Repository
	 */
	protected function getSecondRepository()
	{
		return $this->getModel()->getRepository(substr($this->name, strpos($this->name, 'To') + 2));
	}

	/**
	 * Repository
	 * @return Repository
	 */
	protected function getChildRepository($need = true)
	{
		return $this->getSecondRepository();
	}

	public function getModel($need = true)
	{
		return $this->parent->getModel(NULL);
	}

	/** @deprecated */
	final protected function compare(& $all, $row) {throw new DeprecatedException();}
	/** @deprecated */
	final protected function row($row) {throw new DeprecatedException();}
	/** @deprecated */
	final protected function prepareAllForSet() {throw new DeprecatedException();}
	/** @deprecated */
	final public static function create($className, IEntity $entity, $value = NULL, $name = NULL)	{throw new DeprecatedException();}
}
