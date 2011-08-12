<?php

use Orm\ArrayMapper;

class ArrayMapper_lock_ArrayMapper extends ArrayMapper
{
	public function _lock()
	{
		return $this->lock();
	}

	public function _unlock()
	{
		return $this->unlock();
	}
}
