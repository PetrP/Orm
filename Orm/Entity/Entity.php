<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Exception;

/**
 * Entity
 *
 * @property-read id $id
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity
 */
abstract class Entity extends BaseEntityFragment implements IEntity
{

	/**
	 * @return scalar
	 * @throws EntityNotPersistedException
	 */
	final public function getId()
	{
		$id = $this->getValue('id', false);
		if ($id === NULL) throw new EntityNotPersistedException(EntityHelper::toString($this) . ' is not persisted.');
		return $id;
	}

	/** @return string */
	public function __toString()
	{
		try {
			// todo mozna zrusit
			return isset($this->id) ? (string) $this->id : '';
			// @codeCoverageIgnoreStart
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
		}
	}		// @codeCoverageIgnoreEnd

}
