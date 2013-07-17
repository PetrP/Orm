<?php

/**
 * @covers Orm\Orm
 */
class Orm_require_Test extends TestCase
{

	/**
	 * @runInSeparateProcess
	 * @dataProvider dataProviderClasses
	 */
	public function test($class, $path, $type)
	{
		$this->assertFalse(class_exists('Orm\Orm', false));
		$this->assertFalse($type($class, false)); // interface_exists or class_exists

		require_once $path;

		$this->assertTrue($type($class, false)); // interface_exists or class_exists

		if (stripos($class, 'Exception') === false AND $type !== 'interface_exists')
		{
			$ignore = array(
				'Orm\ObjectMixin' => true,
				'Orm\EntityHelper' => true,
				'Orm\ValidationHelper' => true,
				'Orm\InjectionFactory' => true,
				'Orm\HydrateEntityIterator' => true,
				'Orm\FetchAssoc' => true,
				'Orm\FindByHelper' => true,
				'Orm\DibiResultWrapperIterator' => true,
			);
			if (!isset($ignore[$class]))
			{
				$this->assertTrue(class_exists('Orm\Orm', false));
			}
		}
	}

	public function dataProviderClasses()
	{
		global $ormDir;
		if (!isset($ormDir)) $_ormDir = __DIR__ . '/../../../Orm';
		else $_ormDir = $ormDir;
		$rpOrmDir = realpath($_ormDir);
		$rpOrmDirLen = strlen($rpOrmDir);
		$this->assertTrue($rpOrmDir AND $_ormDir);
		$result = array();
		foreach (array_merge(get_declared_classes(), get_declared_interfaces()) as $class)
		{
			$r = new ReflectionClass($class);
			if ($path = $r->getFileName())
			{
				$path = realpath($path);
				if (strncmp($path, $rpOrmDir, $rpOrmDirLen) === 0)
				{
					$result[] = array($class, $path, $r->isInterface() ? 'interface_exists' : 'class_exists');
				}
			}
		}
		return $result;
	}

}
