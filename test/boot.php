<?php

define('ORM_DIR', __DIR__ . '/../Model');
define('LIBS_DIR', __DIR__ . '/libs');
define('TMP_DIR', __DIR__ . '/tmp');

require_once LIBS_DIR . '/Nette/loader.php';
require_once LIBS_DIR . '/dump.php';
require_once LIBS_DIR . '/dibi/dibi.php';
require_once ORM_DIR . '/loader.php';

Debug::enable(false);
Debug::$strictMode = true;

Environment::setVariable('tempDir', TMP_DIR);
Environment::getRobotLoader()->addDirectory(__DIR__);

require_once ORM_DIR . '/Mappers/Collection/DataSourceCollection.php';

abstract class TestCase extends PHPUnit_Framework_TestCase
{
	public function assertException(Exception $e, $type, $message)
	{
		$this->assertEquals($type, get_class($e));
		$this->assertEquals($e->getMessage(), $message);
	}
}

class Model extends RepositoriesCollection
{
	
}
