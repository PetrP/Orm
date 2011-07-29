<?php

use Orm\RepositoryContainer;

class RepositoryContainer_getRepositoryClass extends RepositoryContainer
{
	public function __getRepositoryClass($name, $deprecated = NULL)
	{
		if ($deprecated === NULL)
		{
			return parent::getRepositoryClass($name);
		}
		return parent::getRepositoryClass($name, $deprecated);
	}
}
