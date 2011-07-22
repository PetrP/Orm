<?php

abstract class ValueEntityFragment_settergetter_Base extends TestCase
{
	abstract protected function a(ValueEntityFragment_gettersetter_Test_Entity $e, $key, $count = NULL, $callmode = 1);

	protected function x($key, $testCount = true)
	{
		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->a($e, $key, $testCount ? 1 : NULL, 0);
		$this->a($e, $key, $testCount ? 2 : NULL, 1);
		$this->a($e, $key, $testCount ? 3 : NULL, 2);
		$this->a($e, $key, $testCount ? 4 : NULL, 3);
		$this->a($e, $key, $testCount ? 4 : NULL, 4); // todo pri tomhle volani se nezavola setter
	}

	protected function php533bugAddExcepted($gOrS, $propertyName, $method)
	{
		if (PHP_VERSION_ID === 50303)
		{
			if ($gOrS == 'g')
			{
				$message = "php 5.3.3 bug #52713; Upgrade php or use \$this->getValue('$propertyName') instead of parent::$method()";
			}
			else
			{
				$message = "php 5.3.3 bug #52713; Upgrade php or use \$this->setValue('$propertyName', \$value) instead of parent::$method(\$value)";
			}
			set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($message) {
				if (!($errno & error_reporting())) return FALSE;
				PHPUnit_Framework_Assert::assertSame(E_USER_WARNING, $errno, $errstr);
				PHPUnit_Framework_Assert::assertSame($message, $errstr);
				restore_error_handler();
			});
		}
	}

}
