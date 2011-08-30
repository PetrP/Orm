<?php

use Orm\DibiManyToManyMapper;

class DibiManyToManyMapper_getConnection_DibiManyToManyMapper extends DibiManyToManyMapper
{
	public function __getConnection()
	{
		return $this->getConnection();
	}
}
