<?php

class MetaData_Test_Entity extends Entity
{
	public static $count = 0;
	public static $metaData;
	public static function createMetaData($entityClass)
	{
		self::$count++;
		return self::$metaData ? self::$metaData : new MetaData($entityClass);
	}

	public function getGetter() {}
	public function setSetter($v) {}
	public function getGetterSetter() {}
	public function setGetterSetter($v) {}
	public function isGetter() {}
	public function isBool() {}

	public static function enum()
	{
		return array('foo' => 'bar');
	}

	const XXX = 'xxx';
	const YYY = 'yyy';
}
