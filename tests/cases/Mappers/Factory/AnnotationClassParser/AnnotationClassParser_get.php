<?php

use Orm\AnnotationClassParser;

class AnnotationClassParser_get_AnnotationClassParser extends AnnotationClassParser
{
	public $a = array();

	protected function getAnnotations(ReflectionClass $reflection)
	{
		$n = $reflection->getName();
		if (isset($this->a[$n])) return $this->a[$n];
		return parent::getAnnotations($reflection);
	}
}


class AnnotationClassParser_get_ArrayObject extends ArrayObject
{

}

class AnnotationClassParser_get_ArrayObject_SplObserver extends ArrayObject implements SplObserver
{
	function update(SplSubject $subject) {}
}

abstract class AnnotationClassParser_get_ArrayObject_Abstract extends ArrayObject {}
class AnnotationClassParser_get_ArrayObject_ParentAbstract extends AnnotationClassParser_get_ArrayObject_Abstract {}
