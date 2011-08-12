<?php

use Orm\Entity;

/**
 * @property $test
 * @property $test2
 */
class ValueEntityFragment_isset_Entity extends Entity
{
	public function getTest2()
	{
		throw new Exception;
	}
}
