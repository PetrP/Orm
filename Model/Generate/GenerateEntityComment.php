<?php

class GenerateEntityComment extends Object
{
	/** @var DibiMapper */
	private $mapper;

	public function __construct(DibiMapper $mapper)
	{
		$this->mapper = $mapper;
	}

	private function getTableName(DibiMapper $mapper)
	{
		if (!class_exists('MockDibiMapper__')) eval('
			class MockDibiMapper__ extends DibiMapper
			{
				static function gtn(DibiMapper $mapper) {return $mapper->getTableName();}
			}
		');
		return MockDibiMapper__::gtn($mapper);
	}

	public function getComment()
	{
		$types = array(
			'INT' => 'int',
			'SMALLINT' => 'int',
			'TINYINT' => 'int',
			'BIGINT' => 'int',
			'ENUM' => 'mixed',
			'VARCHAR' => 'string',
			'TEXT' => 'string',
			'CHAR' => 'string',
			'DOUBLE' => 'float',
			'FLOAT' => 'float',
			'FLOAT UNSIGNED' => 'float',
			'DECIMAL' => 'float',
			'DATE' => 'DateTime',
			'DATETIME' => 'DateTime',
			'TIMESTAMP' => 'DateTime',
		);

		$conventional = $this->mapper->getConventional();
		$comments = array();
		foreach ($this->mapper->connection->getDriver()->getColumns($this->getTableName($this->mapper)) as $meta)
		{
			$meta = (object) $meta;
			$name = key($conventional->formatStorageToEntity(array($meta->name => NULL)));
			if ($name === 'id') continue;
			$comment = array();

			$bool = false;
			$type = $types[$meta->nativetype];
			if ($meta->nativetype === 'TINYINT' AND $meta->size == 1)
			{
				$bool = true;
				$type = 'bool';
			}
			if ($meta->nullable) $type .= '|NULL';

			$comment[] = " * @property $type \${$name}";

			if ($meta->nativetype === 'ENUM')
			{
				$e = preg_replace('#^enum\((.*)\)$#', '$1', $meta->vendor['Type']);
				$comment[] = "{enum {$e}}";
			}
			if ($meta->default !== NULL AND $meta->default !== '')
			{
				$d = $bool ? ($meta->default ? 'true' : 'false') : $meta->default;
				$comment[] = "{default {$d}}";
			}

			$comments[] = implode(' ', $comment);
		}

		$comments = implode("\n", $comments);
		return "/**\n$comments\n */";
	}
}
