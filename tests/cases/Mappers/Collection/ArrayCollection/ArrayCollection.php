<?php

use Orm\ArrayCollection;

class ArrayCollection_ArrayCollection extends ArrayCollection
{
	public static function set(ArrayCollection $c, $property, $value)
	{
		$p = new ReflectionProperty($c, $property);
		setAccessible($p);
		$p->setValue($c, $value);
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
 * @property TestEntity|NULL $e
 * @property mixed $mixed
 * @property mixed $phpBug50688
 */
class ArrayCollection_Entity extends TestEntity
{
	public function __construct($int = NULL, $string = NULL)
	{
		parent::__construct();
		$this->int = $int;
		$this->string = $string;
	}

	public function getGetter()
	{
		return $this->int;
	}

	public function getPhpBug50688()
	{
		try { throw new Exception; } catch (Exception $e) {}
		return parent::getPhpBug50688();
	}
}
