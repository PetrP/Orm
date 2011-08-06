<?php

/**
 * @covers Orm\IDatabaseConventional
 */
class IDatabaseConventional_Test extends InterfaceTestCase
{
	protected $interface = 'Orm\IDatabaseConventional';
	protected $methodCounts = 6;

	public function testImplements()
	{
		$this->assertTrue($this->reflection->implementsInterface('Orm\IConventional'));
	}

	public function testMethods()
	{
		$this->assertMethod('formatEntityToStorage', '$data');
		$this->assertMethod('formatStorageToEntity', '$data');
		$this->assertMethod('getPrimaryKey', '');
		$this->assertMethod('getTable', 'Orm\IRepository $repository');
		$this->assertMethod('getManyToManyTable', 'Orm\IRepository $source, Orm\IRepository $target');
		$this->assertMethod('getManyToManyParam', '$param');
	}
}
