<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use ArrayObject;
use DateTime;
use Exception;

/**
 * Helper, which uses entity to validation.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\Helpers
 */
class ValidationHelper
{

	/**
	 * Je value validni?
	 * Kdyz je to vhodne tak se value pretypuje.
	 * @param array
	 * @param mixed
	 * @return bool
	 */
	public static function isValid(array $types, & $value)
	{
		$_value = $value;
		$_e = NULL;

		if (isset($types['mixed']) OR !$types) return true;

		foreach ($types as $type)
		{
			if ($type === 'null')
			{
				if ($value === NULL) return true;
				continue;
			}
			else if ($type === 'id')
			{
				if ($value !== TRUE AND !empty($value) AND is_scalar($value) AND ctype_digit((string) $value))
				{
					return true;
				}
				continue;
			}
			else if (!in_array($type, array('string', 'float', 'int', 'bool', 'array', 'object', 'mixed', 'scalar')))
			{
				if ($value instanceof $type) return true;
				else if ($type === 'datetime' AND $value !== '' AND (is_string($value) OR is_int($value) OR is_float($value)))
				{
					try {
						$_value = static::createDateTime($value);
					} catch (Exception $e) {
						$_e = $e;
					}
				}
				else if ($type === 'arrayobject' AND is_string($value) AND in_array(substr($value, 0, 1), array('O','C')) AND substr($value, 1, 18) === ':11:"ArrayObject":' AND ($tmp = @unserialize($value)) instanceof ArrayObject) // intentionally @
				{
					$_value = $tmp;
				}
				continue;
			}
			else if ($type === 'mixed') return true;
			else
			{
				if (call_user_func("is_$type", $value)) return true;
				else
				{
					if (in_array($type, array('float', 'int')) AND (is_numeric($value)))
					{
						$_value = $value;
						settype($_value, $type);
					}
					else if (in_array($type, array('array', 'object')) AND (is_array($value) OR is_object($value)))
					{
						$_value = $value;
						settype($_value, $type);
					}
					else if ($type === 'string' AND (is_int($value) OR is_float($value) OR (is_object($value) AND method_exists($value, '__toString'))))
					{
						$_value = (string) $value;
					}
					else if ($type === 'array' AND is_string($value) AND substr($value, 0, 2) === 'a:' AND is_array($tmp = @unserialize($value))) // intentionally @
					{
						$_value = $tmp;
					}
					else if ($type === 'bool' AND in_array($value, array(0, 0.0, '0', 1, 1.0, '1'), true))
					{
						$_value = (bool) $value;
					}
					else if (is_string($value) AND in_array($type, array('float', 'int')))
					{
						// todo anglickej zpusob zadavani dat 100,000.00 tady uplne zkolabuje
						if (!isset($intValue)) $intValue = str_replace(array(',', ' '), array('.', ''), $value);
						if (is_numeric($intValue))
						{
							$_value = $intValue;
							settype($_value, $type);
						}
					}
					continue;
				}
			}

		}

		if ($_value === $value)
		{
			if ($_e)
			{
				throw $_e;
			}
			return false;
		}
		else
		{
			$value = $_value;
			return true;
		}

	}

	/**
	 * Email validator: is value valid email address?
	 * @param string
	 * @return bool
	 * @author David Grudl
	 */
	public static function isEmail($value)
	{
		$atom = "[-a-z0-9!#$%&'*+/=?^_`{|}~]"; // RFC 5322 unquoted characters in local-part
		$localPart = "(?:\"(?:[ !\\x23-\\x5B\\x5D-\\x7E]*|\\\\[ -~])+\"|$atom+(?:\\.$atom+)*)"; // quoted or unquoted
		$chars = "a-z0-9\x80-\xFF"; // superset of IDN
		$domain = "[$chars](?:[-$chars]{0,61}[$chars])"; // RFC 1034 one domain component
		return (bool) preg_match("(^$localPart@(?:$domain?\\.)+[-$chars]{2,19}\\z)i", $value);
	}

	/**
	 * URL validator: is value valid URL?
	 * @param string
	 * @return bool
	 * @author David Grudl
	 */
	public static function isUrl($value)
	{
		return (bool) preg_match('/^.+\.[a-z]{2,6}(?:\\/.*)?$/i', $value);
	}

	/**
	 * DateTime object factory.
	 * @param  string|int|DateTime
	 * @return DateTime
	 */
	public static function createDateTime($time)
	{
		if ($time instanceof DateTime)
		{
			return clone $time;
		}
		else if (is_numeric($time))
		{
			if ($time <= 31557600) // year
			{
				$time += time();
			}
			return new DateTime(date('Y-m-d H:i:s', $time));
		}
		// textual or NULL
		return new DateTime($time);
	}

}
