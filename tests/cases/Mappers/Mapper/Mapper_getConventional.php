<?php

use Orm\ArrayMapper;

class Mapper_getConventional_Mapper extends ArrayMapper
{
	public $c;
	protected function createConventional()
	{
		if ($this->c) return $this->c;
		return parent::createConventional();
	}
}
