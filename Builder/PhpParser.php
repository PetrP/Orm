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
					$token .= 'extract(OrmClosureFix::$vars[\'.OrmClosureFix::uses(array('
						. preg_replace('#&?\s*\$([^,\s]+)#', "'\$1'=>\$0", $parser->fetchUntil(')'))
						. ')).\'], EXTR_REFS);';
					$parser->fetch(')');
				}
				$body = '';
				do {
					$body .= $parser->fetchUntil('}') . '}';
				} while ($parser->fetch() && !$parser->isNext(',', ';', ')'));

				if (strpos($body, 'function(')) {
					throw new Exception("Nested closure in $file");
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
	public static function replaceGlobalScopeRenames($s)
	{
		static $classes;
		if ($classes === NULL)
		{
			$classes = array();
			foreach (array(
				'DeprecatedException',
				'InvalidArgumentException',
				'NotImplementedException',
				'NotSupportedException',
			) as $class)
			{
				$classes[" $class "] = " Orm$class ";
				$classes[" $class::"] = " Orm$class::";
				$classes[" $class;"] = " Orm$class;";
				$classes[" $class("] = " Orm$class(";
				$classes["($class "] = "(Orm$class ";
				$classes[", $class "] = ", Orm$class ";
				$classes["Orm\\$class"] = "Orm\\Orm$class";
			}
		}
		return strtr($s, $classes);
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
		if ($orm)
		{
			$data = preg_replace('#namespace\s+Orm\\\\?[a-z0-9_\\\\\s]*;\n\n#si', '', $data);
			$data = preg_replace('#namespace\s+HttpPHPUnit\\\\?[a-z0-9_\\\\\s]*;\n\n#si', '', $data);
		}
		$inNamespace = (bool) preg_match('#namespace\s+([a-z0-9_\\\\\s]+);#si', $data);

		$data = preg_replace_callback('#(use\s+)([a-z0-9_\\\\\s]+)(;\n\n?)#si', function (array $m) use ($inNamespace, $orm, $nette) {
			if (!$nette AND strpos($m[2], 'Nette') === 0)
			{
				return $m[0];
			}
			if ($nette AND $inNamespace AND strpos($m[2], 'Nette') === 0)
			{
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
		if ($nette)
		{
			$data = preg_replace('#\\\\?Nette\\\\\\\\?([a-z0-9_]+[^\\\\a-z0-9_])#si', '$1', $data);
			$data = preg_replace('#\\\\?Nette\\\\\\\\?[a-z0-9_]+\\\\\\\\?([a-z0-9_]+)#si', '$1', $data);
		}
		return $data;
	}

	/**
	 * @param string
	 * @param int
	 * @param bool
	 * @return string
	 */
	public static function buildInfo($data, $version, $isDev = false)
	{
		static $head;
		static $tag;
		if ($head === NULL)
		{
			$git = new Git(__DIR__ . '/..');
			$head = $git->getSha('HEAD');
			$tags = array();
			foreach (array_filter(explode("\n", $git->command('show-ref --tags'))) as $t)
			{
				list($tsha, $tname) = explode(' ', $t);
				$tname = substr($tname, strrpos($tname, '/')+1);
				if (!preg_match('#^v[0-9]+\.[0-9]+\.[0-9]+$#s', $tname)) continue;
				if (trim($git->command("cat-file -t $tsha")) === 'tag')
				{
					$tagContent = $git->command("cat-file tag $tsha");
					if (preg_match('#^object ([0-9a-f]{40})\n#', $tagContent, $match))
					{
						if ($match[1] === $head)
						{
							$tagDate = 'unknown';
							if (preg_match('#\ntagger [^\>]+> ([0-9]+) #', $tagContent, $match))
							{
								$d = \Nette\DateTime::from($match[1]);
								$tagDate = $d->format('Y-m-d');
							}
							$tags[] = array($tname, $tagDate);
						}
					}
				}
				else if ($tsha === $head)
				{
					$tags[] = array($tname, 'unknown');
				}
			}
			if (!$tags)
			{
				if (!$isDev) throw new Exception('Add dev parametr to url: run.php?dev');
				$tags = array(array('0.0.0.dev', '0000-00-00'));
			}
			if (count($tags) > 1) throw new Exception;
			$tag = current($tags);
			$tag[0] = ltrim($tag[0], 'v');
		}

		$_head = $head; // protoze nejaky bug pri static variable a closure use
		$_tag = $tag;
		$data = preg_replace_callback('#(?:/\*)?\<build\:\:([^\>]+)\>(?:\*/[^/]*/\*\*/)?#s', function (array $m) use ($version, $_head, $_tag) {
			$m = $m[1];
			if ($m === 'orm')
			{
				return $version & Builder::NS ? '5.3' : '5.2';
			}
			else if ($m === 'nette')
			{
				if ($version & Builder::PREFIXED_NETTE) return '5.2 prefixed';
				if ($version & Builder::NONNS_NETTE) return '5.2 without prefixes';
				if ($version & Builder::NS_NETTE) return '5.3';
			}
			else if ($m === 'date')
			{
				return $_tag[1];
			}
			else if ($m === 'revision')
			{
				return substr($_head, 0, 7);
			}
			else if ($m === 'version')
			{
				return $_tag[0];
			}
			else if ($m === 'version_id')
			{
				$tmp = explode('.', $_tag[0]);
				return $tmp[0] * 10000 + $tmp[1] * 100 + $tmp[2];
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
		$s = preg_replace_callback('#\s?/\*§php52([^§]*)php52§\*/\s?#s', function ($m) use ($php52) {
			if ($php52)
			{
				return str_replace('* /', '*/', $m[1]);
			}
		}, $s);
		$s = preg_replace_callback('#\s?/\*§php53\*/([^§]*)/\*php53§\*/\s?#s', function ($m) use ($php52) {
			if (!$php52)
			{
				return str_replace('* /', '*/', $m[1]);
			}
		}, $s);
		return $s;
	}
}
