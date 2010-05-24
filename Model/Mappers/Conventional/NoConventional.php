<?php

require_once dirname(__FILE__) . '/IConventional.php';

class NoConventional extends Object implements IConventional
{
	public function format($data, $entityName)
	{
		return (array) $data;
	}

	public function unformat($data, $entityName)
	{
		return (array) $data;
	}

	/**
	 * fk
	 * @param  string
	 * @return string
	 */
	public function foreignKeyFormat($s)
	{
		return $s;
	}

}

