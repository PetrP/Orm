<?php

require dirname(__FILE__) . '/base.php';

NetteTestHelpers::skip();

$strings = array();
$chars = str_split('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789_');
for ($i = 10000 ;$i--;)
{
	$string = '';
	shuffle($chars);
	foreach ($chars as $char) $string .= $char;
	$strings[] = $string;
}


Debug::timer();
foreach ($strings as $string)
{
	$string = ucfirst($string);
}
dt(Debug::timer(), 'ucfirst');
if (ucfirst(end($strings)) !== $string) dt($string, end($strings));

Debug::timer();
foreach ($strings as $string)
{
	$string{0} = strtoupper($string{0});
}
dt(Debug::timer(), 'strtoupper');
if (ucfirst(end($strings)) !== $string) dt($string, end($strings));

Debug::timer();
foreach ($strings as $string)
{
	$string{0} = $string{0} & "\xDF";
}
dt(Debug::timer(), '{0}\xDF');
if (ucfirst(end($strings)) !== $string) dt($string, end($strings));

Debug::timer();
foreach ($strings as $string)
{
	$string[0] = $string[0] & "\xDF";
}
dt(Debug::timer(), '[0]\xDF');
if (ucfirst(end($strings)) !== $string) dt($string, end($strings));








Debug::timer();
foreach ($strings as $string)
{
	$string = lcfirst($string);
}
dt(Debug::timer(), 'lcfirst');
if (lcfirst(end($strings)) !== $string) dt($string, end($strings));

Debug::timer();
foreach ($strings as $string)
{
	$string{0} = strtolower($string{0});
}
dt(Debug::timer(), 'strtolower');
if (lcfirst(end($strings)) !== $string) dt($string, end($strings));

Debug::timer();
foreach ($strings as $string)
{
	$string{0} = $string{0} | "\x20";
}
dt(Debug::timer(), '{0}\xDF');
if (lcfirst(end($strings)) !== $string) dt($string, end($strings));

Debug::timer();
foreach ($strings as $string)
{
	$string[0] = $string[0] | "\x20";
}
dt(Debug::timer(), '[0]\x20');
if (lcfirst(end($strings)) !== $string) dt($string, end($strings));

Debug::timer();
foreach ($strings as $string)
{
	if ($string{0} != '_') $string{0} = $string{0} | "\x20";

}
dt(Debug::timer(), '==_\x20');
if (lcfirst(end($strings)) !== $string) dt($string, end($strings));
Debug::timer();
foreach ($strings as $string)
{
	if ($string{0} !== '_') $string{0} = $string{0} | "\x20";

}
dt(Debug::timer(), '===_\x20');
if (lcfirst(end($strings)) !== $string) dt($string, end($strings));
/*

dt("B" | "\x20");
dt("b" | "\x20");
dt("_" | "\x20");
dt("1" | "\x20");
dt("z" | "\x20");
dt("Z" | "\x20");

 *//*
for ($i=10000;$i--;)
{
	if (("B" | chr($i)) === 'b')
		if (("b" | chr($i)) === 'b')
		//	if (("_" | chr($i)) === '_')
	dt($i);
}
 */



__halt_compiler();
------EXPECT------
