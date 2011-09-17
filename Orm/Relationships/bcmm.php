<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Exception;

/**
 * @deprecated
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships
 */
abstract class OldManyToMany extends ManyToMany
{
	protected $parentRepository;
	protected $childRepository;

	private $parent;

	/**
	 * @param IEntity
	 */
	public function __construct(IEntity $parent, $repository, $childParam, $parentParam, $mapped, $value = NULL)
	{
		if (!strpos(get_class($this), 'To')) throw new Exception(); // todo
		$this->parent = $parent;
		$firstRepository = $this->getFirstRepository();
		$secondRepository = $this->getSecondRepository();

		if ($firstRepository->isAttachableEntity($parent))
		{
			$this->parentRepository = $firstRepository;
			$this->childRepository = $secondRepository;
			$mapped = RelationshipLoader::MAPPED_HERE;
		}
		else if ($this->getSecondRepository()->isAttachableEntity($parent))
		{
			$this->parentRepository = $secondRepository;
			$this->childRepository = $firstRepository;
			$mapped = RelationshipLoader::MAPPED_THERE;
		}
		else
		{
			throw new InvalidEntityException;
		}

		parent::__construct($parent, $this->childRepository, $childParam, $parentParam, $mapped, $value);
	}

	public function getModel($need = true)
	{
		return $this->parent->getModel(NULL);
	}

	/** @return Repository */
	protected function getFirstRepository()
	{
		return $this->getModel()->getRepository(substr(get_class($this), 0, strpos(get_class($this), 'To')));
	}

	/** @return Repository */
	protected function getSecondRepository()
	{
		return $this->getModel()->getRepository(substr(get_class($this), strpos(get_class($this), 'To') + 2));
	}

	/** @deprecated*/
	final protected function createMapper(IRepository $firstRepository, IRepository $secondRepository){throw new DeprecatedException('Use Orm\Mapper::createManyToManyMapper');/*// todo array jen kdyz mam na obou stranach arraymapper a mam protejsi property (protoze pole je potreba udrzovat na obou stranach)*/}
	/** @deprecated*/
	final protected function getFirstParamName() {throw new DeprecatedException();}
	/** @deprecated*/
	final protected function getSecondParamName() {throw new DeprecatedException();}
	/** @deprecated*/
	final protected function getParentParamName() {throw new DeprecatedException();}
	/** @deprecated*/
	final protected function getChildParamName() {throw new DeprecatedException();}
	/** @deprecated */
	final public static function create($className, IEntity $entity, $value = NULL, $name = NULL)	{throw new DeprecatedException();}
}
