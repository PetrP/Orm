<?php

/** Dump */
function d($var)
{
	if (func_num_args() > 1) $var = func_get_args();
	return Debug::dump($var);
}

/** Bar dump */
function dd($var)
{
	if (func_num_args() > 1) $var = func_get_args();
	else if (is_array($var)) $var = array(NULL => $var);
	return Debug::barDump($var);
}
