<?php

use Orm\ArrayManyToManyMapper;

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers Orm\ArrayManyToManyMapper::setParams
 */
class ArrayManyToManyMapper_deprecated_Test extends TestCase
{
	private $mm;

	protected function setUp()
	{
		$this->mm = new ArrayManyToManyMapper;
	}

	public function testSetParams()
	{
		$this->setExpectedException('Nette\DeprecatedException');
		$this->mm->setParams(false);
	}

}
