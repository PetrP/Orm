<?php

class MockAnnotationMetaData extends AnnotationMetaData
{
	public static $mock;

	public static function getMetaData($class)
	{
		$a = new self($class);
		return $a->metaData;
	}

	protected function getAnnotation($class)
	{
		if ($class === 'AnnotationMetaData_MockEntity') return self::$mock;
		return AnnotationsParser::getAll(new ClassReflection($class));
	}

	public static function mockConstruct($class)
	{
		return new AnnotationMetaData($class);
	}
}

class AnnotationMetaData_MockEntity extends Entity
{

}

class AnnotationMetaData_ManyToMany extends OldManyToMany {};
class AnnotationMetaData_OneToMany extends OldOneToMany {};
