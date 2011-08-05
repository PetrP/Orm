<?php

use Orm\ArrayMapper;

class ArrayMapper_getConventional_ArrayMapper extends ArrayMapper
{
	public $c;
	protected function createConventional()
	{
		if ($this->c) return $this->c;
		return parent::createConventional();
	}
}
