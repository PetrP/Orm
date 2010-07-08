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
dump(Debug::timer(), 'ucfirst');
if (ucfirst(end($strings)) !== $string) dump($string, end($strings));

Debug::timer();
foreach ($strings as $string)
{
	$string{0} = strtoupper($string{0});
}
dump(Debug::timer(), 'strtoupper');
if (ucfirst(end($strings)) !== $string) dump($string, end($strings));

Debug::timer();
foreach ($strings as $string)
{
	$string{0} = $string{0} & "\xDF";
}
dump(Debug::timer(), '{0}\xDF');
if (ucfirst(end($strings)) !== $string) dump($string, end($strings));

Debug::timer();
foreach ($strings as $string)
{
	$string[0] = $string[0] & "\xDF";
}
dump(Debug::timer(), '[0]\xDF');
if (ucfirst(end($strings)) !== $string) dump($string, end($strings));








Debug::timer();
foreach ($strings as $string)
{
	$string = lcfirst($string);
}
dump(Debug::timer(), 'lcfirst');
if (lcfirst(end($strings)) !== $string) dump($string, end($strings));

Debug::timer();
foreach ($strings as $string)
{
	$string{0} = strtolower($string{0});
}
dump(Debug::timer(), 'strtolower');
if (lcfirst(end($strings)) !== $string) dump($string, end($strings));

Debug::timer();
foreach ($strings as $string)
{
	$string{0} = $string{0} | "\x20";
}
dump(Debug::timer(), '{0}\xDF');
if (lcfirst(end($strings)) !== $string) dump($string, end($strings));

Debug::timer();
foreach ($strings as $string)
{
	$string[0] = $string[0] | "\x20";
}
dump(Debug::timer(), '[0]\x20');
if (lcfirst(end($strings)) !== $string) dump($string, end($strings));

Debug::timer();
foreach ($strings as $string)
{
	if ($string{0} != '_') $string{0} = $string{0} | "\x20";

}
dump(Debug::timer(), '==_\x20');
if (lcfirst(end($strings)) !== $string) dump($string, end($strings));
Debug::timer();
foreach ($strings as $string)
{
	if ($string{0} !== '_') $string{0} = $string{0} | "\x20";

}
dump(Debug::timer(), '===_\x20');
if (lcfirst(end($strings)) !== $string) dump($string, end($strings));
/*

dump("B" | "\x20");
dump("b" | "\x20");
dump("_" | "\x20");
dump("1" | "\x20");
dump("z" | "\x20");
dump("Z" | "\x20");

 *//*
for ($i=10000;$i--;)
{
	if (("B" | chr($i)) === 'b')
		if (("b" | chr($i)) === 'b')
		//	if (("_" | chr($i)) === '_')
	dump($i);
}
 */



__halt_compiler();
------EXPECT------
