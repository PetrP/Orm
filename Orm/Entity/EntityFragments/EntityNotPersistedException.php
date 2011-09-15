<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use RuntimeException;

/**
 * Entity is not persisted.
 * Requested operation requires it.
 * Call IRepository::persist() first.
 * @see IRepository::persist()
 */
class EntityNotPersistedException extends RuntimeException
{

}
