<?php

namespace Orm;

use Nette\Object;
use Nette\InvalidStateException;
use ReflectionClass;

require_once dirname(__FILE__) . '/IMapper.php';
require_once dirname(__FILE__) . '/Conventional/NoConventional.php';

/**
 * @property-read IRepository $repository
 * @property-read IConventional $conventional
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

	/** @return RepositoryContainer */
	final public function getModel()
	{
		return $this->repository->getModel();
	}

	/**
	 * @see self::createConventional()
	 * @return IConventional
	 */
	final public function getConventional()
	{
		if (!isset($this->conventional))
		{
			$conventional = $this->createConventional();
			if (!($conventional instanceof IConventional))
			{
				throw new InvalidStateException(get_class($this) . '::createConventional() must return Orm\IConventional');
			}
			$this->conventional = $conventional;
		}
		return $this->conventional;
	}

	/**
	 * Vola automaticky findBy* a getBy*
	 * <pre>
	 * 	$mapper->findByAuthor(3);
	 * 	// stejne jako
	 * 	$mapper->findBy(array('author' => 3));
	 *
	 * 	$mapper->findByAuthorAndCategory(3, 'foo');
	 * 	// stejne jako
	 * 	$mapper->findBy(array('author' => 3, 'category' => 'foo'));
	 * </pre>
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
	 * @throws InvalidStateException
	 */
	final protected function getCollectionClass($info = false)
	{
		if (!isset($this->collectionClass))
		{
			$class = $this->createCollectionClass();
			if (!class_exists($class))
			{
				throw new InvalidStateException("Collection '{$class}' doesn't exists");
			}
			$reflection = new ReflectionClass($class);
			if (!$reflection->implementsInterface('Orm\IEntityCollection'))
			{
				throw new InvalidStateException("Collection '{$class}' must implement Orm\\IEntityCollection");
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
