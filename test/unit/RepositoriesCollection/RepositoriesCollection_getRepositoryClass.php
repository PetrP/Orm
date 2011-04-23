<?php

class RepositoriesCollection_getRepositoryClass extends AutoLoader
{
	public $last;
	public function tryLoad($type)
	{
		$this->last = $type;
	}
}
