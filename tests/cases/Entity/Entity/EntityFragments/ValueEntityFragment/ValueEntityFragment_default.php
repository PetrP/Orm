<?php

use Orm\Entity;

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
 *
 * @property $setterNoSet
 */
class ValueEntityFragment_default_Entity extends Entity
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
		throw Exception;
	}

	protected function getDefaultTestMethodAndMeta()
	{
		return 'method';
	}


	public $countSetterNoSet = 0;
	protected function getDefaultSetterNoSet()
	{
		$this->countSetterNoSet++;
		return 'method';
	}
	public function setSetterNoSet()
	{
		return $this;
	}

}
