<?php

/**
 * @deprecated
 */
abstract class OldManyToMany extends ManyToMany
{
	protected $parentRepository;
	protected $childRepository;

	private $parent;

	/**
	 * @param IEntity
	 */
	public function __construct(IEntity $parent, $repository, $childParam, $parentParam, $mappedByParent, $value = NULL)
	{
		if (!strpos(get_class($this), 'To')) throw new Exception(); // todo
		$this->parent = $parent;
		$firstRepository = $this->getFirstRepository();
		$secondRepository = $this->getSecondRepository();

		if ($firstRepository->isEntity($parent))
		{
			$this->parentRepository = $firstRepository;
			$this->childRepository = $secondRepository;
			$mappedByParent = true;
		}
		else if ($this->getSecondRepository()->isEntity($parent))
		{
			$this->parentRepository = $secondRepository;
			$this->childRepository = $firstRepository;
			$mappedByParent = false;
		}
		else
		{
			throw new UnexpectedValueException();
		}

		parent::__construct($parent, $this->childRepository, $childParam, $parentParam, $mappedByParent, $value);
	}

	public function getModel()
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
	final protected function createMapper(IRepository $firstRepository, IRepository $secondRepository){throw new DeprecatedException('Use Mapper::createManyToManyMapper');/*// todo array jen kdyz mam na obou stranach arraymapper a mam protejsi property (protoze pole je potreba udrzovat na obou stranach)*/}
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
