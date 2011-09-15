<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Closure;
use NCallback;

/**
 * PHP callback encapsulation.
 *
 * @author David Grudl
 * @copyright Nette Framework
 */
final class Callback extends Object
{
	/** @var string|array|Closure */
	private $cb;

	/**
	 * Callback factory.
	 * @param mixed class, object, function, callback
	 * @param string method
	 * @return Callback
	 */
	public static function create($callback, $m = NULL)
	{
		if ($m === NULL AND $callback instanceof Callback)
		{
			return $callback;
		}
		return new Callback($callback, $m);
	}

	/**
	 * @param mixed class, object, function, callback
	 * @return bool
	 */
	public static function is($callback)
	{
		if ($callback instanceof Callback)
		{
			return true;
		}
		static $c = '\Nette\Callback'; // php52
		if ($callback instanceof $c)
		{
			return true;
		}
		if ($callback instanceof NCallback)
		{
			return true;
		}
		if ($callback instanceof /*§php53*/\Callback/*php53§*//*§php52Callbackphp52§*/)
		{
			return true;
		}
		return false;
	}

	/**
	 * @see self::create()
	 * @param mixed class, object, function, callback
	 * @param string method
	 */
	protected function __construct($t, $m = NULL)
	{
		if ($m === NULL)
		{
			if (is_string($t))
			{
				$t = explode('::', $t, 2);
				$this->cb = isset($t[1]) ? $t : $t[0];
			}
			else if (is_object($t))
			{
				if ($t instanceof Closure)
				{
					$this->cb = $t;
				}
				else if (Callback::is($t))
				{
					$this->cb = $t->getNative();
				}
				else
				{
					$this->cb = array($t, '__invoke');
				}
			}
			else
			{
				$this->cb = $t;
			}
		}
		else
		{
			$this->cb = array($t, $m);
		}

		if (!is_callable($this->cb, TRUE))
		{
			throw new InvalidArgumentException("Invalid callback.");
		}
	}

	/**
	 * Invokes callback. Do not call directly.
	 * @return mixed
	 * @throws NotCallableException
	 */
	public function __invoke()
	{
		return $this->invokeArgs(func_get_args());
	}

	/**
	 * Invokes callback.
	 * @return mixed
	 * @throws NotCallableException
	 */
	public function invoke()
	{
		return $this->invokeArgs(func_get_args());
	}

	/**
	 * Invokes callback with an array of parameters.
	 * @param array
	 * @return mixed
	 * @throws NotCallableException
	 */
	public function invokeArgs(array $args)
	{
		if (!is_callable($this->cb))
		{
			throw new NotCallableException("Callback '$this' is not callable.");
		}
		return call_user_func_array($this->cb, $args);
	}

	/**
	 * Returns PHP callback pseudotype.
	 * @return string|array|Closure
	 */
	public function getNative()
	{
		return $this->cb;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		if ($this->cb instanceof \Closure)
		{
			return '{closure}';
		}
		else if (is_string($this->cb) && $this->cb[0] === "\0")
		{
			return '{lambda}';
		}
		is_callable($this->cb, TRUE, $textual);
		return $textual;
	}
/*§php52

	/** @internal * /
	static $vars = array();

	/** @internal * /
	static function uses($args)
	{
		self::$vars[] = $args;
		return count(self::$vars)-1;
	}
php52§*/

}
