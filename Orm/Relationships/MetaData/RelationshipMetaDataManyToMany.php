<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * MetaData for Orm\ManyToMany.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships\MetaData
 */
class RelationshipMetaDataManyToMany extends RelationshipMetaDataToMany
{

	/** @var RelationshipMetaDataToMany::MAPPED_* */
	private $mapped;

	/**
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param bool|NULL
	 */
	public function __construct($parentEntityName, $parentParam, $childRepositoryName, $childParam, $relationshipClass, $mapped = NULL)
	{
		if (!$childParam)
		{
			$mapped = self::MAPPED_HERE;
		}
		parent::__construct(MetaData::ManyToMany, $parentEntityName, $parentParam, $childRepositoryName, $childParam, $relationshipClass);
		$this->mapped = (bool) $mapped;
	}

	/**
	 * Kontroluje asociace z druhe strany
	 * @param IRepositoryContainer
	 */
	public function check(IRepositoryContainer $model)
	{
		parent::check($model);

		$this->checkIntegrity($model, MetaData::ManyToMany, true, array($this, 'checkIntegrityCallback'));
	}

	/** @return mixed RelationshipMetaDataToMany::MAPPED_* */
	final public function getWhereIsMapped()
	{
		return $this->mapped;
	}

	/**
	 * @param RelationshipMetaData
	 * @param RelationshipMetaData
	 */
	protected function checkIntegrityCallback(RelationshipMetaData $parent, RelationshipMetaData $child)
	{
		if ($parent === $child)
		{
			$parent->mapped = self::MAPPED_BOTH;
		}
		else
		{
			$pm = $parent->getWhereIsMapped();
			$cm = $child->getWhereIsMapped();
			if ($pm === self::MAPPED_HERE AND $cm === self::MAPPED_HERE)
			{
				throw new RelationshipLoaderException("{$parent->parentEntityName}::\${$parent->parentParam} a {$child->parentEntityName}::\${$parent->childParam} {{$parent->type}} u ubou je nastaveno ze se na jeho strane ma mapovat, je potreba vybrat a mapovat jen podle jedne strany");
			}
			if ($pm === self::MAPPED_THERE AND $cm === self::MAPPED_THERE)
			{
				throw new RelationshipLoaderException("{$parent->parentEntityName}::\${$parent->parentParam} a {$child->parentEntityName}::\${$parent->childParam} {{$parent->type}} ani u jednoho neni nastaveno ze se podle neho ma mapovat. např: {m:m {$parent->repository} {$parent->childParam} mapped}");
			}
		}
	}

}
