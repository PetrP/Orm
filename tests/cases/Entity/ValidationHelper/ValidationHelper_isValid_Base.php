<?php

use Orm\ValidationHelper;

abstract class ValidationHelper_isValid_Base extends TestCase
{
	protected $type;
	protected function t($value, $result, $valueChangeTo = NULL, $same = true)
	{
		$this->type = is_array($this->type) ? $this->type : explode('|', $this->type);
		$_value = $value;
		$this->assertSame(ValidationHelper::isValid($this->type, $value), $result);
		$valueChangeTo = ($result AND func_num_args() >= 3) ? func_get_arg(2) : $_value;
		$this->{$same ? 'assertSame' : 'assertEquals'}($value, $valueChangeTo);
		if ($value === NULL OR $value === false) $this->assertTrue($value === $valueChangeTo);
	}
}
