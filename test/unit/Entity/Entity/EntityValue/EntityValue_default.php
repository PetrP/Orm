<?php

/**
 * @property $meta {default meta}
 *
 * @property $noDefault
 *
 * @property $testMethod1
 * @property $testMethod2
 * @property $testMethod3
 *
 * @property $testMethodAndMeta {default meta}
 */
class EntityValue_default_Entity extends Entity
{
	public $count = 0;
	protected function getDefaultTestMethod1()
	{
		$this->count++;
		return 'testMethod1';
	}

	public function getDefaultTestMethod2()
	{
		return 'testMethod2';
	}

	private function getDefaultTestMethod3()
	{
		throw new InvalidStateException;
	}

	protected function getDefaultTestMethodAndMeta()
	{
		return 'method';
	}

}
