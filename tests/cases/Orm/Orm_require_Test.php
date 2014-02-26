<?php

use Orm\Orm;

class Orm_require_Test extends TestCase
{

	/**
	 * @runInSeparateProcess
	 */
	public function testMain()
	{
		global $ormDir;
		$this->assertFalse(class_exists('Orm\Orm', false));
		$this->assertFalse(class_exists('Orm\Entity', false));
		require_once (isset($ormDir) ? $ormDir : __DIR__ . '/../../../Orm') . '/Orm.php';
		$this->assertTrue(class_exists('Orm\Orm', false));
		$this->assertTrue(class_exists('Orm\Entity', false));
	}

	/**
	 * @runInSeparateProcess
	 * @dataProvider dataProviderClasses
	 * @covers Orm\Orm
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
					$result[$class] = array($class, $path, $r->isInterface() ? 'interface_exists' : 'class_exists');
				}
			}
		}

		$isCodeCoverageEnabled = extension_loaded('xdebug') ? (function_exists('xdebug_code_coverage_started') ? xdebug_code_coverage_started() : true) : false;
		if (!$isCodeCoverageEnabled OR Orm::VERSION_ID !== 0)
		{
			// @runInSeparateProcess trva prilis dlouho.
			// Takze spustime jen dulezite tridy a jednu nahodne, aby se overilo ze je vse v poradku.
			// Vsechny se spousti na vyvojove verzi kdyz je zapnuty code coverage.
			// Vse s code coverage spoustim před vydadavanim verze.
			// Tento test neni tak dulezity aby se casove vyplatilo ho pokazde spoustet.
			$result = array(
				$tmp = 'Orm\Entity' => $result[$tmp],
				$tmp = array_rand($result) => $result[$tmp],
			);
		}
		return $result;
	}

}
