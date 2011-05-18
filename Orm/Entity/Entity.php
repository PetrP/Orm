<?php

namespace Orm;

use Nette\InvalidStateException;
use Exception;

require_once dirname(__FILE__) . '/IEntity.php';

require_once dirname(__FILE__) . '/MetaData/AnnotationMetaData.php';

require_once dirname(__FILE__) . '/ValidationHelper.php';

require_once dirname(__FILE__) . '/EntityToArray.php';

require_once dirname(__FILE__) . '/_EntityEvent.php';
require_once dirname(__FILE__) . '/_EntityGeneratingRepository.php';
require_once dirname(__FILE__) . '/_EntityValue.php';
require_once dirname(__FILE__) . '/_EntityBase.php';


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
		} catch (Exception $e) {
			Debug::toStringException($e);
		}
	}

}
