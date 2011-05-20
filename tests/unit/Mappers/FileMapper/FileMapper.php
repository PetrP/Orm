<?php

use Orm\FileMapper;

class FileMapper_FileMapper extends FileMapper
{
	protected function getFilePath()
	{
		return TMP_DIR . '/' . __CLASS__ . '.data';
	}
}

