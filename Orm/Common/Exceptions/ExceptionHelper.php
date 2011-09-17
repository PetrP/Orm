<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Helper for easy creating exception messages.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Common\Exceptions
 */
class ExceptionHelper
{

	/**
	 * @param string|array
	 * @param string
	 * @return string
	 */
	public static function format($message, $format)
	{
		if (is_array($message))
		{
			$data = array_merge(array(NULL), array_values($message)); unset($data[0]);
			while (preg_match('#<%([0-9&!\|]+)%([^<>]*)>#is', $format))
			$format = preg_replace_callback('#<%([0-9&!\|]+)%([^<>]*)>#is', function ($m) use ($data) {
				foreach (explode('&', $m[1]) as $i)
				{
					$r = false;
					foreach (explode('|', $i) as $i)
					{
						$neg = $i{0} === '!';
						if (($neg AND empty($data[ltrim($i, '!')])) OR (!$neg AND !empty($data[$i])))
						{
							$r = true;
							break;
						}
					}
					if (!$r) return NULL;
				}
				return $m[2];
			}, $format);
			$class = get_called_class();
			$format = preg_replace_callback('#%(.)([0-9])#si', function ($m) use ($data, $class) {
				return call_user_func(array($class, $m[1]), isset($data[$m[2]]) ? $data[$m[2]] : NULL);
			}, $format);
			$message = $format;
		}
		return $message;
	}

	/**
	 * @param string|object
	 * @return string
	 */
	public static function c($class)
	{
		return is_object($class) ? get_class($class) : $class;
	}

	/**
	 * @param string
	 * @return string
	 */
	public static function s($string)
	{
		return $string;
	}

	/**
	 * @param mixed
	 * @return string
	 */
	public static function t($type)
	{
		return is_object($type) ? get_class($type) : gettype($type);
	}

	/**
	 * @param mixed
	 * @return string
	 */
	public static function v($value)
	{
		return (is_scalar($value) AND !is_bool($value)) ? $value : static::t($value);
	}

}
