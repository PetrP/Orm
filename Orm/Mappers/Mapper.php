<?php

namespace Orm;


require_once dirname(__FILE__) . '/IMapper.php';

require_once dirname(__FILE__) . '/Conventional/NoConventional.php';


abstract class Mapper extends Object implements IMapper
{
	abstract public function findAll();
	abstract public function persist(IEntity $entity);
	abstract public function remove(IEntity $entity);
	abstract public function begin(); // todo rename?
	abstract public function flush();
	abstract public function createManyToManyMapper($firstParam, IRepository $repository, $secondParam);

	protected $repository;

	private $conventional;

	private $collectionClass;

	public function __construct(IRepository $repository)
	{
		$this->repository = $repository;
	}

	public function getRepository()
	{
		return $this->repository;
	}

	final public function getModel()
	{
		return $this->repository->getModel();
	}

	/** @return IConventional */
	public function getConventional()
	{
		if (!isset($this->conventional))
		{
			$conventional = $this->createConventional();
			if (!($conventional instanceof IConventional))
			{
				throw new InvalidStateException(get_class($this) . '::createConventional() must return IConventional');
			}
			$this->conventional = $conventional;
		}
		return $this->conventional;
	}

	protected function createConventional()
	{
		return new NoConventional($this);
	}

	final protected function getCollectionClass($info = false)
	{
		if (!isset($this->collectionClass))
		{
			$class = $this->createCollectionClass();
			if (!class_exists($class))
			{
				throw new InvalidStateException("Collection '{$class}' doesn't exists");
			}
			$reflection = new ClassReflection($class);
			if (!$reflection->implementsInterface('IEntityCollection'))
			{
				throw new InvalidStateException("Collection '{$class}' must implement IEntityCollection");
			}
			else if ($reflection->isAbstract())
			{
				throw new InvalidStateException("Collection '{$class}' is abstract.");
			}
			else if (!$reflection->isInstantiable())
			{
				throw new InvalidStateException("Collection '{$class}' isn't instantiable");
			}
			$this->collectionClass = array($class, NULL);

			if ($class === 'DibiCollection' OR is_subclass_of($class, 'DibiCollection'))
			{
				$this->collectionClass[1] = 'dibi';
			}
			else if ($class === 'DataSourceCollection' OR is_subclass_of($class, 'DataSourceCollection'))
			{
				$this->collectionClass[1] = 'datasource';
			}
			else if ($class === 'ArrayCollection' OR is_subclass_of($class, 'ArrayCollection'))
			{
				$this->collectionClass[1] = 'array';
			}
		}
		return $info ? $this->collectionClass : $this->collectionClass[0];
	}

	abstract protected function createCollectionClass();

	public function __call($name, $args)
	{
		if (!method_exists($this, $name))
		{
			if (strncasecmp($name, 'findBy', 6) === 0 OR strncasecmp($name, 'getBy', 5) === 0)
			{
				return call_user_func_array(array($this->findAll(), $name), $args);
			}
		}
		return parent::__call($name, $args);
	}

	final public function findBy(array $where)
	{
		return $this->findAll()->findBy($where);
	}

	final public function getBy(array $where)
	{
		return $this->findAll()->getBy($where);
	}

	/** @deprecated */
	final public function delete(IEntity $entity){throw new DeprecatedException('Use Mapper::remove() instead');}
	/** @deprecated */
	final public function createDefaultManyToManyMapper(){throw new DeprecatedException('Use Mapper::createManyToManyMapper() instead');}
}
