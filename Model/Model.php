<?php

require_once dirname(__FILE__) . '/Entity/Entity.php';

require_once dirname(__FILE__) . '/Repository/Repository.php';

require_once dirname(__FILE__) . '/Mappers/Mapper.php';

/**
 * Kolekce Repository.
 * Stara se o jejich vytvareni.
 * Je to vstupni bod do modelu z jinych casti aplikace.
 *
 * V kazdem projektu si vytvorte tridu model:
 * <pre>
 * /**
 *  * @property-read ArticlesRepository $articles
 *  * @property-read UsersRepository $users
 *  *⁄
 * class Model extends RepositoriesCollection
 * {
 *
 * }
 * </pre>
 * Psat anotace neni nutne, ale pomaha ide napovidat.
 *
 * Na repository se pristupuje jako k property:
 * <pre>
 *
 * $model; // instanceof Model
 *
 * $model->articles; // instanceof ArticlesRepository
 *
 * $article = $model->articles->getById(1); // instanceof Article
 *
 * </pre>
 *
 * Do aplikace model vsunete napriklad takto:
 * <pre>
 * // config.ini
 * service.Model = Model
 *
 * // BasePresenter.php
 * /**
 *  * @property-read Model $model
 *  *⁄
 * abstract class BasePresenter extends Presenter
 * {
 * 	/** @var Model *⁄
 * 	private $model;
 *
 *  /** @return Model *⁄
 * 	public function getModel()
 * 	{
 * 		if (!isset($this->model))
 * 		{
 * 			$this->model = $this->context->getService('Model');
 * 		}
 * 		return $this->model;
 * 	}
 * }
 * </pre>
 */
abstract class RepositoriesCollection extends Object
{
	/** @var Model @deprecated @todo di @see self::get() */
	static private $instance;

	/** @var array repositoryName => IRepository */
	private $repositories = array();

	public function __construct()
	{
		if (!isset(self::$instance))
		{
			self::$instance = $this;
		}
	}

	/**
	 * Vraci prvni vytvoreny model, je pro zpetnou kompatibilitu.
	 * A zatim jeste neni uplne vymysleno jak se bez toho obejit.
	 * Bohuzel zatim pouziva: Entity::getModel(), Entity::setValueHelper(), ManyToMany, MetaDataProperty::setOneToOne()
	 * @return Model
	 * @todo di
	 * @deprecated
	 */
	public static function get()
	{
		if (!isset(self::$instance))
		{
			throw new InvalidStateException();
		}
		return self::$instance;
	}

	/**
	 * Dej mi instanci repository.
	 * @var string repositoryName
	 * @return Repository |IRepository
	 */
	public function getRepository($name)
	{
		$name = strtolower($name);
		if (!isset($this->repositories[$name]))
		{
			$class = $this->getRepositoryClass($name);
			$this->checkRepositoryClass($class, $name);
			$this->repositories[$name] = new $class($this);
		}
		return $this->repositories[$name];
	}

	/**
	 * Existuje repository pod timto nazvem?
	 * @param string repositoryName
	 * @return bool
	 */
	final public function isRepository($name)
	{
		$name = strtolower($name);
		if (isset($this->repositories[$name])) return true;
		return $this->checkRepositoryClass($this->getRepositoryClass($name), $name, false);
	}

	/**
	 * Je tato trida repository?
	 * @param string repositoryClass
	 * @param string repositoryName
	 * @return true or throw exception
	 * @throws InvalidStateException
	 */
	final private function checkRepositoryClass($class, $name, $throw = true)
	{
		if (!class_exists($class))
		{
			if (!$throw) return false;
			throw new InvalidStateException("Repository '{$name}' doesn't exists");
		}

		$reflection = new ClassReflection($class);

		if (!$reflection->implementsInterface('IRepository'))
		{
			if (!$throw) return false;
			throw new InvalidStateException("Repository '{$name}' must implement IRepository");
		}
		else if ($reflection->isAbstract())
		{
			if (!$throw) return false;
			throw new InvalidStateException("Repository '{$name}' is abstract.");
		}
		else if (!$reflection->isInstantiable())
		{
			if (!$throw) return false;
			throw new InvalidStateException("Repository '{$name}' isn't instantiable");
		}

		return true;
	}

	/**
	 * repositoryName > repositoryClass
	 * @param string repositoryName
	 * @return string repositoryClass
	 */
	final private function getRepositoryClass($name)
	{
		$class = $name . 'Repository';
		$class[0] = strtoupper($class[0]);
		return $class;
	}

	/**
	 * <pre>
	 * $model->articles;
	 * </pre>
	 * Do not call directly.
	 * @param string repositoryName
	 * @return IRepository
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
		foreach ($this->repositories as $repo)
		{
			$repo->flush(true);
		}
	}

	/**
	 * Zrusi vsechny zmeny na vsech repository, ale do ukonceni scriptu se zmeny porad drzi.
	 * @todo zrusit i zmeny na entitach, aby se hned vratili do puvodniho stavu.
	 * @return void
	 * @see IRepository::clean()
	 * @see IMapper::clean()
	 */
	final public function clean()
	{
		foreach ($this->repositories as $repo)
		{
			$repo->clean(true);
		}
	}

}

/** @deprecated */
abstract class AbstractModel extends RepositoriesCollection {}
