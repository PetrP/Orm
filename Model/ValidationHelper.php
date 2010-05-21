<?php

class ValidationHelper
{

	public static function isEmail($s)
	{
		$atom = "[-a-z0-9!#$%&'*+/=?^_`{|}~]"; // RFC 5322 unquoted characters in local-part
		$localPart = "(\"([ !\\x23-\\x5B\\x5D-\\x7E]*|\\\\[ -~])+\"|$atom+(\\.$atom+)*)"; // quoted or unquoted
		$chars = "a-z0-9\x80-\xFF"; // superset of IDN
		$domain = "[$chars]([-$chars]{0,61}[$chars])"; // RFC 1034 one domain component
		return (bool) preg_match("(^$localPart@($domain?\\.)+[a-z]{2,14}\\z)i", $s); // strict top-level domain
	}

}
