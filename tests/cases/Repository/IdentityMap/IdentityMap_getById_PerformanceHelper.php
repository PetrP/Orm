<?php

use Orm\PerformanceHelper;

class IdentityMap_getById_PerformanceHelper extends PerformanceHelper
{
	public $access = array();
	public $get;

	public function __construct()
	{
	}

	/**
	 * Rika ze bylo potreba toto id
	 * @param scalar
	 */
	public function access($id)
	{
		$this->access[] = $id;
	}

	/**
	 * Vrati vsechny id ktere asi budou potreba a vyprazdni je.
	 * Lze zavolat jen jednou.
	 * @return array of id
	 */
	public function get()
	{
		$tmp = NULL;
		if ($this->get !== NULL)
		{
			$tmp = $this->get;
			$this->get = NULL;
		}
		return $tmp;
	}
}
