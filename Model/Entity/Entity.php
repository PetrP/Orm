<?php

require_once dirname(__FILE__) . '/IEntity.php';

require_once dirname(__FILE__) . '/EntityManager.php';

require_once dirname(__FILE__) . '/ValidationHelper.php';

require_once dirname(__FILE__) . '/3.php';
require_once dirname(__FILE__) . '/2.php';




/**
 * @property-read int $id
 */
abstract class Entity extends Entity2 implements IEntity
{
	public function __construct()
	{
		parent::__construct();
	}

	protected function check()
	{

	}
	
	public function __toString()
	{
		try {
			// mozna zrusit
			return isset($this->id) ? (string) $this->id : NULL;
		} catch (Exception $e) {
			Debug::toStringException($e);
		}
	}
	
	protected static function getEntityRules($entityClass)
	{
		return EntityManager::getEntityParams($entityClass);
	}
	
	
}
