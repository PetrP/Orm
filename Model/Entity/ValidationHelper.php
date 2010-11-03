<?php

class ValidationHelper
{

	public static function isValid(array $types, & $value)
	{
		$intValue = is_string($value) ? str_replace(array(',', ' '), array('.', ''), $value) : NULL;
		$_value = $value;

		if ($types === array()) return true; // mean mixed

		foreach ($types as $type)
		{
			if ($type === 'void' OR $type === 'null')
			{
				if ($value === NULL) return true;
				continue;
			}
			else if (!in_array($type, array('string', 'float', 'int', 'bool', 'array', 'object')))
			{
				if ($value instanceof $type) return true;
				else if ($type === 'datetime')
				{
					$_value = Tools::createDateTime($value);
				}
				else if ($type === 'arrayobject' AND is_string($value) AND substr($value, 0, 19) === 'O:11:"ArrayObject":' AND ($tmp = @unserialize($value)) instanceof ArrayObject) // intentionally @
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
					if (in_array($type, array('float', 'int')) AND is_numeric($value) OR empty($value))
					{
						$_value = $value;
						settype($_value, $type);
					}
					else if ($intValue !== NULL AND in_array($type, array('float', 'int')) AND is_numeric($intValue))
					{
						$_value = $intValue;
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
					else if ($type === 'bool')
					{
						$_value = (bool) $value;
					}
					continue;
				}
			}

		}

		if ($_value === $value)
		{
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
	 * @param  string
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
	 * @param  string
	 * @return bool
	 * @author David Grudl
	 */
	public static function isUrl($value)
	{
		return (bool) preg_match('/^.+\.[a-z]{2,6}(?:\\/.*)?$/i', $value);
	}

}
