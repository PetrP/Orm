<?php

require_once dirname(__FILE__) . '/IEntity.php';

require_once dirname(__FILE__) . '/AnnotationMetaData.php';

require_once dirname(__FILE__) . '/ValidationHelper.php';

require_once dirname(__FILE__) . '/EntityToArray.php';

require_once dirname(__FILE__) . '/_EntityMeta.php';
require_once dirname(__FILE__) . '/_EntityEvent.php';
require_once dirname(__FILE__) . '/_EntityGeneratingRepository.php';
require_once dirname(__FILE__) . '/_EntityValue.php';
require_once dirname(__FILE__) . '/_EntityBase.php';


/**
 * @property-read int $id
 */
abstract class Entity extends _EntityBase implements IEntity
{

	/** @var int */
	final public function getId()
	{
		$id = $this->getValue('id');
		if (!$id) throw new InvalidStateException('You must persist entity first');
		return $id;
	}

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
