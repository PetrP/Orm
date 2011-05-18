<?php

use Orm\RepositoryContainer;

class RepositoryContainer_getRepositoryClass extends RepositoryContainer
{
	public function getRepositoryClass($name)
	{
		return parent::getRepositoryClass($name);
	}
}
