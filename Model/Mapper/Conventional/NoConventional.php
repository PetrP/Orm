<?php


class Conventional extends Object implements IConventional
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

