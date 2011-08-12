<?php

use Orm\DibiMapper;

class DibiMapper_getConventional_DibiMapper extends DibiMapper
{
	public $c;
	protected function createConventional()
	{
		if ($this->c) return $this->c;
		return parent::createConventional();
	}
}
