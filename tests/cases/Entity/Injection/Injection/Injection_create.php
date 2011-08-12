<?php

use Orm\Injection;

class Injection_create_Injection extends Injection
{

}

class Injection_create_Injection_Constructor extends Injection
{
	public function __construct()
	{

	}
}

class Injection_create_Injection_ConstructorWithParams extends Injection
{
	public function __construct($foo, $bar)
	{

	}
}

class Injection_create_Injection_ConstructorWithParamsNotRequired extends Injection
{
	public function __construct($foo = 3, $bar = NULL)
	{

	}
}
