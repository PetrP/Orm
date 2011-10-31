<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Exception;
use ReflectionClass;

/**
 * Collection of IRepository.
 *
 * Cares of initialisation repository.
 * It is entry point into model from other parts of application.
 * Stores container of services what other objects may need.
 *
 * Repository can be accessed like to property:
 * <code>
 *
 * $orm = new RepositoryContainer;
 *
 * $orm->articles; // instanceof ArticlesRepository
 *
 * $article = $orm->articles->getById(1); // instanceof Article
 *
 * </code>
 *
 * Nette application can reach orm like that:
 * <code>
 * // config.ini
 * service.orm = RepositoryContainer
 *
 * // BasePresenter.php
 * /**
 *  * @property-read RepositoryContainer $orm
 *  * /
 * abstract class BasePresenter extends Presenter
 * {
 * 	/** @var RepositoryContainer * /
 * 	private $orm;
 *
 * 	/** @return RepositoryContainer * /
 * 	public function getOrm()
 * 	{
 * 		if ($this->orm === NULL)
 * 		{
 * 			$this->orm = $this->context->getService('orm');
 * 		}
 * 		return $this->orm;
 * 	}
 * }
 * </code>
 * @author Petr Procházka
 * @package Orm
 * @subpackage RepositoryContainer
 */
class RepositoryContainer extends Object implements IRepositoryContainer
{
	/** @var RepositoryContainer @deprecated @see self::get() */
	static private $instance;

	/** @var array repositoryClass => IRepository */
	private $repositories = array();

	/** @var array repositoryAlias => repositoryClass */
	private $aliases = array();

	/** @var IServiceContainer */
	private $container;

	/** @param IServiceContainerFactory|IServiceContainer|NULL */
	public function __construct($containerFactory = NULL)
	{
		self::$instance = $this;

		if ($containerFactory === NULL)
		{
			$containerFactory = new ServiceContainerFactory;
		}
		if ($containerFactory instanceof IServiceContainerFactory)
		{
			$this->container = $containerFactory->getContainer();
		}
		else if ($containerFactory instanceof IServiceContainer)
		{
			$this->container = $containerFactory;
		}
		else
		{
			throw new InvalidArgumentException(array($this, '__construct() first param', 'Orm\IServiceContainerFactory, Orm\IServiceContainer or NULL', $containerFactory));
		}
	}

	/** @return IServiceContainer */
	public function getContext()
	{
		return $this->container;
	}

	/**
	 * @deprecated
	 * Vraci posledni vytvoreny container, je pro zpetnou kompatibilitu.
	 * Entity::getModel(NULL)
	 * @return RepositoryContainer
	 */
	public static function get()
	{
		if (self::$instance === NULL)
		{
			throw new Exception('RepositoryContainer hasn\'t been instanced yet.');
		}
		if (func_num_args() === 0 OR func_get_arg(0) !== NULL)
		{
			throw new DeprecatedException(__CLASS__ . '::get() is deprecated do not use it.');
		}
		return self::$instance;
	}

	/**
	 * Dej mi instanci repository.
	 * @param string repositoryClassName|alias
	 * @return Repository |IRepository
	 * @throws RepositoryNotFoundException
	 */
	public function getRepository($name)
	{
		$name = strtolower($name);
		if (isset($this->aliases[$name]))
		{
			$class = $this->aliases[$name];
		}
		else
		{
			$old = $this->getRepositoryClass($name, true);
			if (!class_exists($name) AND class_exists($old))
			{
				// bc
				$this->checkRepositoryClass($old, $name, true, $originalClass);
			}
			else
			{
				$this->checkRepositoryClass($name, $name, true, $originalClass);
			}
			$class = $this->aliases[$name] = $originalClass;
		}
		if (!isset($this->repositories[$class]))
		{
			$this->container->freeze();
			$this->repositories[$class] = new $class($this);
		}
		return $this->repositories[$class];
	}

	/**
	 * Registruje repository pod jednodusim nazvem
	 * @param string
	 * @param string
	 * @return IRepositoryContainer
	 * @throws RepositoryAlreadyRegisteredException
	 */
	public function register($alias, $repositoryClass)
	{
		$this->checkRepositoryClass($repositoryClass, $repositoryClass, true, $originClass);
		$alias = strtolower($alias);
		if ($this->isRepository($alias, false))
		{
			throw new RepositoryAlreadyRegisteredException("Repository alias '{$alias}' is already registered");
		}
		$this->aliases[$alias] = $originClass;
		return $this;
	}

	/**
	 * Existuje repository pod timto nazvem?
	 * @param string repositoryClassName|alias
	 * @return bool
	 */
	final public function isRepository($name)
	{
		$name = strtolower($name);
		if (isset($this->aliases[$name])) return true;
		if ($this->checkRepositoryClass($name, $name, false, $originClass))
		{
			$this->aliases[$name] = $originClass;
			return true;
		}
		if (func_num_args() <= 1 OR func_get_arg(1) !== false)
		{
			if ($this->checkRepositoryClass($this->getRepositoryClass($name, true), $name, false, $originClass))
			{
				// bc
				$this->aliases[$name] = $originClass;
				return true;
			}
		}
		return false;
	}

	/**
	 * Je tato trida repository?
	 * @param string repositoryClass
	 * @param string repositoryClassName
	 * @param bool
	 * @param string origin class name
	 * @return true or throw exception
	 * @throws RepositoryNotFoundException
	 */
	final private function checkRepositoryClass($class, $name, $throw = true, & $originClass = NULL)
	{
		if (!class_exists($class))
		{
			if (!$throw) return false;
			throw new RepositoryNotFoundException("Repository '{$name}' doesn't exists");
		}

		$reflection = new ReflectionClass($class);
		$originClass = $reflection->getName();

		if (!$reflection->implementsInterface('Orm\IRepository'))
		{
			if (!$throw) return false;
			throw new RepositoryNotFoundException("Repository '{$originClass}' must implement Orm\\IRepository");
		}
		else if ($reflection->isAbstract())
		{
			if (!$throw) return false;
			throw new RepositoryNotFoundException("Repository '{$originClass}' is abstract.");
		}
		else if (!$reflection->isInstantiable())
		{
			if (!$throw) return false;
			throw new RepositoryNotFoundException("Repository '{$originClass}' isn't instantiable");
		}

		return true;
	}

	/**
	 * @deprecated proto final; je pro bc, neda se s nim upravit chovani
	 * repositoryName > repositoryClass
	 * @param string repositoryName
	 * @return string repositoryClass
	 */
	final protected function getRepositoryClass($name)
	{
		if (func_num_args() < 2 OR func_get_arg(1) !== true)
		{
			throw new DeprecatedException(array(__CLASS__, 'getRepositoryClass() is deprecated; repositoryName', 'class name'));
		}
		if (!$name) return NULL;
		$class = $name . 'Repository';
		$class[0] = strtoupper($class[0]);
		return $class;
	}

	/**
	 * <code>
	 * $orm->articles;
	 * </code>
	 * Do not call directly.
	 * @param string repositoryClassName|alias
	 * @return IRepository
	 * @throws RepositoryNotFoundException
	 */
	public function & __get($name)
	{
		$r = $this->getRepository($name);
		return $r;
	}

	/**
	 * Promitne vsechny zmeny do uloziste na vsech repository.
	 * @return void
	 * @see IRepository::flush()
	 * @see IMapper::flush()
	 */
	public function flush()
	{
		$events = $mappers = array();
		foreach ($this->repositories as $repo)
		{
			$events[] = $repo->getEvents()->fireEvent(Events::FLUSH_BEFORE);
			$mappers[] = $repo->getMapper();
		}
		foreach ($mappers as $mapper)
		{
			$mapper->flush();
		}
		foreach ($events as $event)
		{
			$event->fireEvent(Events::FLUSH_AFTER);
		}
	}

	/**
	 * Zrusi vsechny zmeny na vsech repository, ale do ukonceni scriptu se zmeny porad drzi.
	 * @todo zrusit i zmeny na entitach, aby se hned vratili do puvodniho stavu.
	 * @return void
	 * @see IRepository::clean()
	 * @see IMapper::clean()
	 */
	public function clean()
	{
		$events = $mappers = array();
		foreach ($this->repositories as $repo)
		{
			$events[] = $repo->getEvents()->fireEvent(Events::CLEAN_BEFORE);
			$mappers[] = $repo->getMapper();
		}
		foreach ($mappers as $mapper)
		{
			$mapper->rollback();
		}
		foreach ($events as $event)
		{
			$event->fireEvent(Events::CLEAN_AFTER);
		}
	}

	/** @deprecated */
	final public function setContext()
	{
		throw new DeprecatedException(array($this, 'setContext()', $this, '__construct()'));
	}
}
