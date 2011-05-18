<?php

namespace Orm;


class GenerateDriverMysql extends GenerateDriver
{
	private $footer = array();

	protected function addHeader()
	{
		$this->line('CREATE TABLE %n (', $this->tableName);
	}

	public function addPrimary($name)
	{
		$this->line("\t%n bigint unsigned NOT NULL AUTO_INCREMENT,", $name);
		$this->footer[] = $this->connectionTranslate("\tPRIMARY KEY (%n)", $name);
	}

	protected function addColumn($name, $null, $type)
	{
		$this->line("\t%n %sql %sql,", $name, $type, $null ? 'NULL' : 'NOT NULL');
	}

	public function addString($name, $null)
	{
		$this->addColumn($name, $null, 'varchar(255)');
	}

	public function addInt($name, $null)
	{
		$this->addColumn($name, $null, 'int');
	}

	public function addFloat($name, $null)
	{
		$this->addColumn($name, $null, 'float');
	}

	public function addBool($name, $null)
	{
		$this->addColumn($name, $null, 'tinyint(1) unsigned');
	}

	public function addDatetime($name, $null)
	{
		$this->addColumn($name, $null, 'datetime');
	}

	public function addForeignKey($name, $null)
	{
		$this->addColumn($name, $null, 'bigint unsigned');
	}

	public function addArray($name, $null)
	{
		$this->addMixed($name, $null);
	}

	public function addMixed($name, $null)
	{
		$this->addColumn($name, $null, 'text');
	}

	protected function addFooter()
	{
		$this->line(implode(",\n", $this->footer));
		$this->line(") ENGINE='InnoDB';");
	}
}

class GenerateDriverMysqli extends GenerateDriverMysql {}
