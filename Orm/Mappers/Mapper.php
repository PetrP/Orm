<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use ReflectionClass;

/**
 * Provides mapping between repository and storage.
 * @property-read IRepository $repository
 * @property-read IConventional $conventional
 * @property-read IRepositoryContainer $model
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers
 */
abstract class Mapper extends Object implements IMapper
{
	/** @var IRepository @see self::getRepository() */
	private $repository;

	/** @var IConventional @see self::getConventional() */
	private $conventional;

	/** @var string @see self::getCollectionClass() */
	private $collectionClass;

	/** @param IRepository */
	public function __construct(IRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * Vraci kolekci entit dle kriterii.
	 * @see IEntityCollection::findBy()
	 * @param array
	 * @return IEntityCollection
	 */
	final public function findBy(array $where)
	{
		return $this->findAll()->findBy($where);
	}

	/**
	 * Vraci jednu entitu dle kriterii.
	 * @see IEntityCollection::getBy()
	 * @param array
	 * @return IEntity|NULL
	 */
	final public function getBy(array $where)
	{
		return $this->findAll()->getBy($where);
	}

	/** @return IRepository */
	final public function getRepository()
	{
		return $this->repository;
	}

	/** @return IRepositoryContainer */
	final public function getModel()
	{
		return $this->repository->getModel();
	}

	/**
	 * @see self::createConventional()
	 * @param string $interface internal
	 * @return IConventional
	 */
	public function getConventional()
	{
		if ($this->conventional === NULL)
		{
			$conventional = $this->createConventional();
			$interface = func_num_args() ? func_get_arg(0) : NULL;
			if ($interface !== NULL AND !($conventional instanceof $interface))
			{
				throw new BadReturnException(array($this, 'createConventional', $interface, $conventional));
			}
			if (!($conventional instanceof IConventional))
			{
				throw new BadReturnException(array($this, 'createConventional', 'Orm\IConventional', $conventional));
			}
			$this->conventional = $conventional;
		}
		return $this->conventional;
	}

	/**
	 * Vola automaticky findBy* a getBy*
	 * <code>
	 * 	$mapper->findByAuthor(3);
	 * 	// stejne jako
	 * 	$mapper->findBy(array('author' => 3));
	 *
	 * 	$mapper->findByAuthorAndCategory(3, 'foo');
	 * 	// stejne jako
	 * 	$mapper->findBy(array('author' => 3, 'category' => 'foo'));
	 * </code>
	 * @see self::findBy();
	 * @see self::getBy();
	 * @param string
	 * @param array
	 * @throws MemberAccessException
	 * @return IEntityCollection|IEntity|NULL
	 */
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

	/**
	 * @see self::getConventional()
	 * @return IConventional
	 */
	protected function createConventional()
	{
		return new NoConventional($this);
	}

	/**
	 * Vraci nazev tridy kterou tento mapper pouziva jako IEntityCollection
	 * @see self::getCollectionClass()
	 * @return string
	 */
	abstract protected function createCollectionClass();

	/**
	 * Vraci informace o collection kterou pouziva tento mapper.
	 * @see self::createCollectionClass()
	 * @see ArrayMapper::findAll()
	 * @see DibiMapper::findAll()
	 * @see DibiMapper::dataSource()
	 * @param bool true mean more info (array), false mean just classname
	 * @return array|string array('ClassName', 'dibi|datasource|array') | 'ClassName'
	 * @throws BadReturnException
	 */
	final protected function getCollectionClass($info = false)
	{
		if ($this->collectionClass === NULL)
		{
			$class = $this->createCollectionClass();
			if (!class_exists($class))
			{
				throw new BadReturnException(array($this, 'createCollectionClass', 'Orm\IEntityCollection class name', NULL, "; '{$class}' doesn't exists"));
			}
			$reflection = new ReflectionClass($class);
			if (!$reflection->implementsInterface('Orm\IEntityCollection'))
			{
				throw new BadReturnException(array($this, 'createCollectionClass', 'Orm\IEntityCollection class name', NULL, "; '{$class}' must implement Orm\\IEntityCollection"));
			}
			else if ($reflection->isAbstract())
			{
				throw new BadReturnException(array($this, 'createCollectionClass', 'Orm\IEntityCollection class name', NULL, "; '{$class}' is abstract."));
			}
			else if (!$reflection->isInstantiable())
			{
				throw new BadReturnException(array($this, 'createCollectionClass', 'Orm\IEntityCollection class name', NULL, "; '{$class}' isn't instantiable."));
			}
			$this->collectionClass = array($class, NULL);

			if ($class === 'Orm\DibiCollection' OR is_subclass_of($class, 'Orm\DibiCollection'))
			{
				$this->collectionClass[1] = 'dibi';
			}
			else if ($class === 'Orm\DataSourceCollection' OR is_subclass_of($class, 'Orm\DataSourceCollection'))
			{
				$this->collectionClass[1] = 'datasource';
			}
			else if ($class === 'Orm\ArrayCollection' OR is_subclass_of($class, 'Orm\ArrayCollection'))
			{
				$this->collectionClass[1] = 'array';
			}
		}
		return $info ? $this->collectionClass : $this->collectionClass[0];
	}

}
