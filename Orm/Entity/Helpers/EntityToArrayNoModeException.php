<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use RuntimeException;

/**
 * No or invalid EntityToArray mode given.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\Helpers
 */
class EntityToArrayNoModeException extends RuntimeException
{

	/**
	 * @param string|array
	 * @param int
	 * @param Exception
	 */
	public function __construct($message = NULL, $code = NULL/*§php53*/, Exception $previous = NULL/*php53§*/)
	{
		$message = ExceptionHelper::format($message, '%c1::toArray() no mode for entity; use Orm\EntityToArray::<%2%ENTITY_AS_IS, ENTITY_AS_ID or ENTITY_AS_ARRAY><%3%RELATIONSHIP_AS_IS, RELATIONSHIP_AS_ARRAY_OF_ID or RELATIONSHIP_AS_ARRAY_OF_ARRAY>.');
		parent::__construct($message, $code/*§php53*/, $previous/*php53§*/);
	}
}
