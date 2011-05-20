<?php

use Orm\DibiMapper;

class DibiMapper_getConnection_DibiMapper extends DibiMapper
{
	public $con;
	public function createConnection()
	{
		if ($this->con) return $this->con;
		return new DibiConnection(array('lazy' => true));
	}
}
