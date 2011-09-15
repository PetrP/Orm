<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use RuntimeException;

/**
 * Attempted read from write-only property, write to readonly property or access to undeclared property.
 */
class PropertyAccessException extends RuntimeException
{

}
