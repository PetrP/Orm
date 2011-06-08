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
					$token .= 'extract(NClosureFix::$vars[\'.NClosureFix::uses(array('
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
		return preg_replace('#__DIR__#', 'dirname(__FILE__)', $s);
	}

}
