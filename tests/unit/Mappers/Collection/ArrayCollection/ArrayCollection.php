<?php

use Orm\ArrayCollection;

class ArrayCollection_ArrayCollection extends ArrayCollection
{
	public static function set(ArrayCollection $c, $property, $value)
	{
		$c->$property = $value;
	}

	public static function call(ArrayCollection $c, $method, array $params = array())
	{
		return call_user_func_array(array($c, $method), $params);
	}
}

abstract class ArrayCollection_Base_Test extends TestCase
{
	/** @var ArrayCollection */
	protected $c;

	protected $e;

	protected function setUp()
	{
		$this->e = array(
			new ArrayCollection_Entity(2, 'a'),
			new ArrayCollection_Entity(1, 'b'),
			new ArrayCollection_Entity(3, 'a'),
			new ArrayCollection_Entity(4, 'b'),
		);
		$this->c = new ArrayCollection($this->e);
	}
}

/**
 * @property string|NULL $string
 * @property int|NULL $int
 * @property DateTime|NULL $date
 */
class ArrayCollection_Entity extends TestEntity
{
	public function __construct($int = NULL, $string = NULL)
	{
		parent::__construct();
		$this->int = $int;
		$this->string = $string;
	}
}
