<?php

/**
 * @covers Orm\Orm
 */
class Orm_loader_Test extends TestCase
{

	protected $runTestInSeparateProcess = true;

	public function testIEntity()
	{
		$this->assertFalse(class_exists('Orm\Orm', false));
		$this->assertFalse(interface_exists('Orm\IEntity', false));
		$this->assertTrue(interface_exists('Orm\IEntity'));
		$this->assertTrue(class_exists('Orm\Orm', false));
	}

	public function testEntity()
	{
		$this->assertFalse(class_exists('Orm\Orm', false));
		$this->assertFalse(class_exists('Orm\Entity', false));
		$this->assertTrue(class_exists('Orm\Entity'));
		$this->assertTrue(class_exists('Orm\Orm', false));
	}

	public function testIRepositoryContainer()
	{
		$this->assertFalse(class_exists('Orm\Orm', false));
		$this->assertFalse(interface_exists('Orm\IRepositoryContainer', false));
		$this->assertTrue(interface_exists('Orm\IRepositoryContainer'));
		$this->assertTrue(class_exists('Orm\Orm', false));
	}

	public function testRepositoryContainer()
	{
		$this->assertFalse(class_exists('Orm\Orm', false));
		$this->assertFalse(class_exists('Orm\RepositoryContainer', false));
		$this->assertTrue(class_exists('Orm\RepositoryContainer'));
		$this->assertTrue(class_exists('Orm\Orm', false));
	}

	public function testIRepository()
	{
		$this->assertFalse(class_exists('Orm\Orm', false));
		$this->assertFalse(interface_exists('Orm\IRepository', false));
		$this->assertTrue(interface_exists('Orm\IRepository'));
		$this->assertFalse(class_exists('Orm\Orm', false));
	}

	public function testRepository()
	{
		$this->assertFalse(class_exists('Orm\Orm', false));
		$this->assertFalse(class_exists('Orm\Repository', false));
		$this->assertTrue(class_exists('Orm\Repository'));
		$this->assertFalse(class_exists('Orm\Orm', false));
	}

	protected function prepareTemplate(Text_Template $template)
	{
		parent::prepareTemplate($template);
		$template->setVar(array('included_files' => '
			$_DIR_ = ' . var_export(__DIR__ . '/../..', true) . ';
			require_once $_DIR_ . "/loader.php";
			$robotLoader->addDirectory($_DIR_ . "/../Orm");
			$robotLoader->register();
		'));
	}

}
