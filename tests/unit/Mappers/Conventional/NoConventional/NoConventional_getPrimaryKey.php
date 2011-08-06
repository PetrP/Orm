<?php

use Orm\NoConventional;

class NoConventional_getPrimaryKey_NoConventional extends NoConventional
{
	public function getPrimaryKey()
	{
		return 'foo_bar';
	}
}
