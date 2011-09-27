<?php

use Orm\AnnotationClassParser;
use Orm\AnnotationsParser;

class AnnotationClassParser_get_AnnotationClassParser extends AnnotationClassParser
{
	public $a = array();

	public function __construct()
	{
		$t = $this;
		$normalParser = new AnnotationsParser;
		$parser = new AnnotationsParser(function (ReflectionClass $reflection) use ($t, $normalParser) {
			$n = $reflection->getName();
			if (isset($t->a[$n])) return $t->a[$n];
			return $normalParser->getByReflection($reflection);
		});
		parent::__construct($parser);
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
