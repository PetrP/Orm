<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use LogicException;

/**
 * Entity contains some value, what mapper cannot save.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Exceptions
 */
class MapperPersistenceException extends LogicException
{

	/**
	 * @param string|array
	 * @param int
	 * @param Exception
	 */
	public function __construct($message = NULL, $code = NULL/*§php53*/, Exception $previous = NULL/*php53§*/)
	{
		$message = ExceptionHelper::format($message, "%c1: can't persist %c2::\$%s3; it contains '%t4'.");
		parent::__construct($message, $code/*§php53*/, $previous/*php53§*/);
	}
}
