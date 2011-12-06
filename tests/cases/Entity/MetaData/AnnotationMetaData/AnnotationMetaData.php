<?php

use Orm\AnnotationsParser;
use Orm\AnnotationMetaData;
use Orm\Entity;
use Orm\MetaData;
use Orm\MetaDataProperty;

class MockAnnotationMetaData extends AnnotationMetaData
{
	public static $mock;

	public static function getMetaData($class, AnnotationsParser $parser = NULL, $propertyClass = NULL)
	{
		$m = new MetaData($class, $propertyClass);
		new self($m, new AnnotationsParser(function (ReflectionClass $class) {
			if ($class->getName() === 'AnnotationMetaData_MockEntity') return MockAnnotationMetaData::$mock;
			$p = new AnnotationsParser;
			return $p->getByReflection($class);
		}));
		return $m;
	}

	public static function mockConstruct($class, AnnotationsParser $p = NULL)
	{
		return new AnnotationMetaData(new MetaData($class), $p === NULL ? new AnnotationsParser: $p);
	}
}

class AnnotationMetaData_MockEntity extends Entity
{

}

class AnnotationMetaData_MetaDataProperty extends MetaDataProperty
{
	public function setAbc($cba)
	{
		$this->setDefault("??$cba??");
	}
}

if (PHP_VERSION_ID < 50300)
{
	class SameClass {}
}
