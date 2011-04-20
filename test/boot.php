<?php

require_once __DIR__ . '/libs/nette.min.php';
require_once __DIR__ . '/libs/dump.php';
require_once __DIR__ . '/libs/dibi.min.php';
require_once __DIR__ . '/../../DataSourceX/DataSourceX/extension.php';
require_once __DIR__ . '/../Model/loader.php';

Debug::enable(false);
Debug::$strictMode = true;

Environment::setVariable('tempDir', __DIR__ . '/tmp');
Environment::getRobotLoader()->addDirectory(__DIR__);

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
