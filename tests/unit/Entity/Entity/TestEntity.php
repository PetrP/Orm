<?php

use Orm\Entity;
use Orm\Repository;
use Orm\ArrayMapper;

/**
 * @property string $string {default ''}
 * @property DateTime $date {default 'now'}
 */
class TestEntity extends Entity
{

}

/**
 * @property string $string
 * @property DateTime $date
 */
class TestEntityRepository extends Repository
{
	protected $entityClassName = 'TestEntity';
}

/**
 * @property string $string
 * @property DateTime $date
 */
class TestEntityMapper extends ArrayMapper
{
	private $array = array(
		1 => array(
			'id' => 1,
			'string' => 'string',
			'date' => '2011-11-11',
		),
	);

	protected function loadData()
	{
		return $this->array;
	}

	protected function saveData(array $data)
	{
		$this->array = $data;
	}
}
