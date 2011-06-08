<?php

namespace Orm;

use Nette\InvalidStateException;
use Exception;

require_once __DIR__ . '/IEntity.php';
require_once __DIR__ . '/MetaData/AnnotationMetaData.php';
require_once __DIR__ . '/ValidationHelper.php';
require_once __DIR__ . '/EntityToArray.php';
require_once __DIR__ . '/_EntityEvent.php';
require_once __DIR__ . '/_EntityGeneratingRepository.php';
require_once __DIR__ . '/_EntityValue.php';
require_once __DIR__ . '/_EntityBase.php';
require_once __DIR__ . '/EntityHelper.php';

/**
 * @property-read id $id
 */
abstract class Entity extends _EntityBase implements IEntity
{

	/** @return scalar */
	final public function getId()
	{
		$id = $this->getValue('id', false);
		if (!$id) throw new InvalidStateException('You must persist entity first');
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
			Debug::toStringException($e);
		}
	}		// @codeCoverageIgnoreEnd

}
