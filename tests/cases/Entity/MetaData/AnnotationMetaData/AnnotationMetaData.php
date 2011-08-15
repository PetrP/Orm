<?php

use Nette\Reflection\AnnotationsParser;
use Orm\AnnotationMetaData;
use Orm\Entity;
use Orm\OldManyToMany;
use Orm\OldOneToMany;
use Orm\MetaData;

class MockAnnotationMetaData extends AnnotationMetaData
{
	public static $mock;

	public static function getMetaData($class)
	{
		$m = new MetaData($class);
		new self($m);
		return $m;
	}

	protected function getAnnotation($class)
	{
		if ($class === 'AnnotationMetaData_MockEntity') return self::$mock;
		return AnnotationsParser::getAll(new ReflectionClass($class));
	}

	public static function mockConstruct($class)
	{
		return new AnnotationMetaData(new MetaData($class));
	}
}

class AnnotationMetaData_MockEntity extends Entity
{

}

class AnnotationMetaData_ManyToMany extends OldManyToMany {};
class AnnotationMetaData_OneToMany extends OldOneToMany {};

if (PHP_VERSION_ID < 50300)
{
	class SameClass {}
}
