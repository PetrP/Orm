<?php

class DibiMapper_getPrimaryKey_DibiMapper extends DibiMapper_getConventional_DibiMapper
{
	public function __getPrimaryKey()
	{
		return $this->getPrimaryKey();
	}
}
