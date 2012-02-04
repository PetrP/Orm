<?php

use Orm\ObjectMixin;

abstract class TestCase extends PHPUnit_Framework_TestCase
{

	public function __call($name, $args)
	{
		return ObjectMixin::call($this, $name, $args);
	}

	public static function __callStatic($name, $args)
	{
		return ObjectMixin::callStatic(get_called_class(), $name, $args);
	}

	public function &__get($name)
	{
		return ObjectMixin::get($this, $name);
	}

	public function __set($name, $value)
	{
		return ObjectMixin::set($this, $name, $value);
	}

	public function __isset($name)
	{
		return ObjectMixin::has($this, $name);
	}

	public function __unset($name)
	{
		ObjectMixin::remove($this, $name);
	}

	protected function prepareTemplate(Text_Template $template)
	{
		global $ormDir;
		$template->setVar(array('constants' => ''));
		$template->setVar(array('included_files' => '
			$_DIR_ = ' . var_export(__DIR__ . '/..', true) . ';
			$ormDir = ' . var_export(isset($ormDir) ? $ormDir : NULL, true) . ';
			require_once $_DIR_ . "/loader.php";
			if (!isset($ormDir)) $ormDir = $_DIR_ . "/../Orm";
			$robotLoader->addDirectory($ormDir);
			$robotLoader->register();
		'));
	}

}
