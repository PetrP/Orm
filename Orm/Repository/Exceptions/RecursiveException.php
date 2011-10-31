<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * There is an infinite recursion during persist.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Exceptions
 */
class RecursiveException extends InvalidEntityException
{

}
