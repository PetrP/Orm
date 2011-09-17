<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use RuntimeException;

/**
 * Repository with this name is already exists.
 * @author Petr Procházka
 * @package Orm
 * @subpackage RepositoryContainer\Exceptions
 */
class RepositoryAlreadyRegisteredException extends RuntimeException
{

}
