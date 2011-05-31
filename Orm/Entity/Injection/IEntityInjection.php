<?php

namespace Orm;

interface IEntityInjection
{
	function getInjectedValue();

	function setInjectedValue($value);
}
