<?php

require_once dirname(__FILE__) . '/IConventional.php';

class NoConventional extends Object implements IConventional
{
	public function __construct(Mapper $repository)
	{

	}

	public function format($data)
	{
		return (array) $data;
	}

	public function unformat($data)
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

