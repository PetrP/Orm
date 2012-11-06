<?php

namespace Orm\Builder;

use Nette\Utils\Tokenizer;
use Exception;

/**
 * Simple tokenizer for PHP.
 */
class PhpParser extends Tokenizer
{

	/**
	 * @param string
	 * @author David Grudl
	 */
	public function __construct($code)
	{
		$this->ignored = array(T_COMMENT, T_DOC_COMMENT, T_WHITESPACE);
		foreach (token_get_all($code) as $token)
		{
			$this->tokens[] = is_array($token) ? self::createToken($token[1], $token[0]) : $token;
		}
	}

	/**
	 * @param string
	 * @return string
	 * @author David Grudl
	 */
	public static function replaceClosures($s)
	{
		// replace closures with create_function
		$parser = new PhpParser($s);
		$s = '';
		while (($token = $parser->fetch()) !== FALSE) {
			if ($parser->isCurrent(T_FUNCTION) && $parser->isNext('(')) { // lamda functions
				$parser->fetch('(');
				$token = "create_function('" . $parser->fetchUntil(')') . "', '";
				$parser->fetch(')');
				if ($use = $parser->fetch(T_USE)) {
					$parser->fetch('(');
					$token .= 'extract(OrmCallback::$vars[\'.OrmCallback::uses(array('
						. preg_replace('#&?\s*\$([^,\s]+)#', "'\$1'=>\$0", $parser->fetchUntil(')'))
						. ')).\'], EXTR_REFS);';
					$parser->fetch(')');
				}
				$body = '';
				do {
					$body .= $parser->fetchUntil('}') . '}';
				} while ($parser->fetch() && !$parser->isNext(',', ';', ')'));

				if (strpos($body, 'function (') OR strpos($body, 'function(')) {
					throw new \Exception("Nested closure.");
				}

				$token .= substr(var_export(substr(trim($body), 1, -1), TRUE), 1, -1) . "')";
			}
			$s .= $token;
		}
		return $s;
	}

	/**
	 * @param string
	 * @return string
	 */
	public static function replaceDirConstant($s)
	{
		return str_replace('__DIR__', 'dirname(__FILE__)', $s);
	}

	/**
	 * @param string
	 * @return string
	 */
	public static function replaceLateStaticBinding($s)
	{
		$s = str_replace('new static', 'new self', $s);
		$s = str_replace('static::', 'self::', $s);
		$s = str_replace('get_called_class()', '__CLASS__', $s);
		return $s;
	}

	/**
	 * @param string
	 * @return string
	 */
	public static function replaceGlobalScopeRenames($data)
	{
		static $classes = array(
			'DeprecatedException',
			'InvalidArgumentException',
			'NotImplementedException',
			'NotSupportedException',
			'MemberAccessException',
			'ObjectMixin',
			'Object',
			'Callback',
			'AnnotationsParser',
		);
		static $tmp1;
		static $tmp2;
		if ($tmp1 === NULL)
		{
			$tmp1 = $tmp2 = array();
			foreach ($classes as $class)
			{
				$tmp2[$class] = $tmp = "Orm$class";
				$tmp1[" $class "] = " $tmp ";
				$tmp1[" $class\n"] = " $tmp\n";
				$tmp1[" $class::"] = " $tmp::";
				$tmp1["Orm\\$class"] = "Orm\\$tmp";
			}
		}

		$replace2 = $tmp2;
		$replace1 = $tmp1;

		$inNamespaceHttpPHPUnit = (bool) preg_match('#namespace\s+HttpPHPUnit([a-z0-9_\\\\\s]*);#si', $data);
		if ($inNamespaceHttpPHPUnit)
		{
			unset($replace2['Object']);
			unset($replace1[' Object ']);
			unset($replace1[" Object\n"]);
			unset($replace1[' Object::']);
			unset($replace1['Orm\Object']);
		}

		$parser = new PhpParser($data);
		$s = '';
		$last = false;
		while (($token = $parser->fetch()) !== FALSE)
		{
			if ($parser->isCurrent(T_COMMENT, T_DOC_COMMENT, T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE))
			{
				$token = strtr($token, $replace1);
			}
			if (!$parser->isCurrent(T_COMMENT, T_DOC_COMMENT, T_WHITESPACE))
			{
				if ($parser->isCurrent(T_STRING) AND isset($replace2[$token]) AND ($last OR $parser->isNext(T_DOUBLE_COLON)))
				{
					$token = $replace2[$token];
				}
				$last = $parser->isCurrent(T_NEW, T_CLASS, T_EXTENDS, T_INSTANCEOF, '(', ',');
			}
			$s .= $token;
		}

		return $s;
	}

	/**
	 * Standardize line endings to unix-like
	 * @param string
	 * @return string
	 */
	public static function standardizeLineEndings($s)
	{
		$s = str_replace("\r\n", "\n", $s); // DOS
		$s = strtr($s, "\r", "\n"); // Mac
		return $s;
	}

	/**
	 * Odstrani namespace as use.
	 * @param string
	 * @param bool
	 * @param bool
	 * @return string
	 */
	public static function removeNamespace($data, $orm, $nette)
	{
		if ($orm OR $nette)
		{
			$inNamespaceOrm = (bool) preg_match('#namespace\s+Orm([a-z0-9_\\\\\s]*);#si', $data);
			if ($orm)
			{
				$data = preg_replace('#namespace\s+Orm\\\\?[a-z0-9_\\\\\s]*;\n\n#si', '', $data);
				$data = preg_replace('#namespace\s+HttpPHPUnit\\\\?[a-z0-9_\\\\\s]*;\n\n#si', '', $data);
			}
			$inNamespace = (bool) preg_match('#namespace\s+([a-z0-9_\\\\\s]+);#si', $data);

			$data = preg_replace_callback('#(use\s+)([a-z0-9_\\\\\s]+)(;\n\n?)#si', function (array $m) use ($inNamespace, $inNamespaceOrm, $orm, $nette) {
				if (!$inNamespaceOrm)
				{
					if (!$nette AND strpos($m[2], 'Nette') === 0)
					{
						return $m[0];
					}
					if ($nette AND $inNamespace AND strpos($m[2], 'Nette') === 0)
					{
						if (preg_match('#\sas\s+(.+)$#si', $m[2], $mm))
						{
							return $m[1] . $mm[1] . $m[3];
						}
						return $m[1] . substr($m[2], strrpos($m[2], '\\')+1) . $m[3];
					}
					if ($orm AND $inNamespace AND (strpos($m[2], 'Orm') === 0 OR strpos($m[2], 'HttpPHPUnit') === 0))
					{
						return $m[1] . substr($m[2], strrpos($m[2], '\\')+1) . $m[3];
					}
					if (!$orm AND !$inNamespace AND (strpos($m[2], 'Orm') === 0 OR strpos($m[2], 'HttpPHPUnit') === 0))
					{
						return $m[0];
					}
				}
				if ($inNamespace)
				{
					return $m[0];
				}
				return NULL;
			}, $data);

			if ($orm)
			{
				$data = preg_replace('#\\\\?Orm\\\\\\\\?([a-z0-9_])#si', '$1', $data);
				$data = preg_replace('#\\\\?HttpPHPUnit\\\\\\\\?([a-z0-9_])#si', '$1', $data);
			}
			if (!$inNamespaceOrm AND $nette)
			{
				$data = preg_replace('#\\\\?Nette\\\\\\\\?([a-z0-9_]+[^\\\\a-z0-9_])#si', '$1', $data);
				$data = preg_replace('#\\\\?Nette\\\\\\\\?[a-z0-9_]+\\\\\\\\?([a-z0-9_]+)#si', '$1', $data);
			}
		}
		return $data;
	}

	/**
	 * @param string
	 * @param int
	 * @param VersionInfo
	 * @return string
	 */
	public static function buildInfo($data, $version, VersionInfo $info)
	{
		$data = preg_replace_callback('#(?:/\*)?\<build\:\:([^\>]+)\>(?:\*/[^/]*/\*\*/)?#s', function (array $m) use ($version, $info) {
			$m = $m[1];
			if ($m === 'orm')
			{
				return $version & Builder::NS ? '5.3' : '5.2';
			}
			else if ($m === 'date')
			{
				return $info->date;
			}
			else if ($m === 'revision')
			{
				return $info->shortSha;
			}
			else if ($m === 'version')
			{
				return $info->version;
			}
			else if ($m === 'version_id')
			{
				return $info->versionId;
			}
			throw new Exception($m);
		}, $data);

		return $data;
	}

	/**
	 * @param string
	 * @return string
	 */
	public static function versionFix($s, $php52)
	{
		$s = preg_replace_callback('#/\*§php52\n?([^§]*)php52§\*/\n?#s', function ($m) use ($php52) {
			if ($php52)
			{
				return str_replace('* /', '*/', $m[1]);
			}
		}, $s);
		$s = preg_replace_callback('#/\*§php53\*/\n?([^§]*)/\*php53§\*/\n?#s', function ($m) use ($php52) {
			if (!$php52)
			{
				return str_replace('* /', '*/', $m[1]);
			}
		}, $s);
		return $s;
	}
}
