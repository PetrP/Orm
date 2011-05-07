<?php

abstract class EntityValue_settergetter_Base extends TestCase
{
	abstract protected function a(EntityValue_gettersetter_Test_Entity $e, $key, $count = NULL, $callmode = 1);

	protected function x($key, $testCount = true)
	{
		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, $testCount ? 1 : NULL, 0);
		$this->a($e, $key, $testCount ? 2 : NULL, 1);
		$this->a($e, $key, $testCount ? 3 : NULL, 2);
		$this->a($e, $key, $testCount ? 4 : NULL, 3);
		$this->a($e, $key, $testCount ? 4 : NULL, 4); // todo pri tomhle volani se nezavola setter
	}

}
