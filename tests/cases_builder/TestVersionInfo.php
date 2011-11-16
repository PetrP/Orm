<?php

use Nette\NotImplementedException;

class TestVersionInfo extends Orm\Builder\VersionInfo
{

	public function __construct($tag, $date = 'now')
	{
		$this->tag = $tag;
		$this->version = ltrim($this->tag, 'v');
		$this->versionId = $this->parseId($this->version);
		$this->date = new DateTime($date);
		$this->date = $this->date->format('Y-m-d');
		$this->sha = '0000000000000000000000000000000000000000';
		$this->shortSha = '00000000';
	}

}
