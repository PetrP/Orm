<?php


class StdObject extends ArrayObject
{
	public function __construct($input = array(), $flags = self::ARRAY_AS_PROPS, $iterator_class = 'ArrayIterator')
	{
		parent::__construct($input, $flags, $iterator_class);
	}
}
