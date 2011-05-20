<?php

use Orm\ArrayMapper;

class ArrayMapper_createCollectionClass_ArrayMapper extends ArrayMapper
{
	public function __createCollectionClass()
	{
		return $this->createCollectionClass();
	}
}
