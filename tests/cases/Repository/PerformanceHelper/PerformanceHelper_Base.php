<?php

use Orm\PerformanceHelper;

class PerformanceHelper_Base_PerformanceHelper extends PerformanceHelper
{
	/** @return Nette\Caching\Cache */
	public function __getCache()
	{
		return parent::getCache();
	}
}

class PerformanceHelper_ArrayObject extends ArrayObject
{
	public $lastIndex;

	public function offsetExists($index)
	{
		$this->lastIndex = $index;
		return parent::offsetExists($index);
	}

}
