<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use RuntimeException;

/**
 * Container is frozen. You can't add or remove his service.
 * @author Petr Procházka
 * @package Orm
 * @subpackage DI\Exceptions
 */
class FrozenContainerException extends RuntimeException
{

}
