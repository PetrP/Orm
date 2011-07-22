<?php

use Nette\InvalidStateException;
use Orm\Entity;

/**
 * @property $meta
 * @property-read $metaPrivate
 */
class BaseEntityFragment_setValues_Entity extends Entity
{
	public $property;
	protected $propertyPrivate;
	private $propertyPrivate2;

	public $_method;

	public function setMethod($value)
	{
		$this->_method = $value;
	}

	protected function setMethodPrivate($value)
	{
		throw new InvalidStateException();
	}

	private function setMethodPrivate2($value)
	{
		throw new InvalidStateException();
	}

}
