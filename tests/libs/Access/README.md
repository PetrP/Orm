Access
======

Tool for access to private and protected members of object. It's handy for unit tests.


Property
--------

```php
class Foo
{
	private $foo;
}

$a = Access(new Foo, '$foo');

$a->set(3);
assert(3, $a->get());
```


Method
------

```php
class Foo
{
	private function foo()
	{
		return 4;
	}
}

$a = Access(new Foo, 'foo');

assert(4, $a->call());
```


Whole class
-----------

```php
class Foo
{
	private $foo;

	private function bar($plus)
	{
		return $this->foo + $plus;
	}
}

$a = Access(new Foo);

$a->foo = 10;
assert(10, $a->foo);
assert(11, $a->bar(1));
```


Requirements
------------
Library has no external dependencies.

Fully works with PHP >= 5.3.2.
PHP >= 5.2.0 is supported partially (see below).

AccessMethod require PHP 5.3.2 or later.
AccessProperty require PHP 5.3.0 or later.

PHP >= 5.2.0 AND < 5.3.2 not supported:
 * Final classes.
 * Private methods.
 * Read private static property.
 * Write private property.


Instalations
------------

### GitHub

Each version is tagged and available on [download page](https://github.com/PetrP/Access/tags).

```php
require_once __DIR__ . '/libs/Access/Init.php';
```

### Composer & Packagist

Access is available on [Packagist](http://packagist.org/packages/PetrP/Access), where you can get it via [Composer](http://getcomposer.org).


Author
-------
Petr Procházka
http://petrp.cz petr@petrp.cz


License
-------
"New" BSD License

```
Copyright (c) Petr Procházka
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
		* Redistributions of source code must retain the above copyright
			notice, this list of conditions and the following disclaimer.
		* Redistributions in binary form must reproduce the above copyright
			notice, this list of conditions and the following disclaimer in the
			documentation and/or other materials provided with the distribution.
		* Neither the name of the <organization> nor the
			names of its contributors may be used to endorse or promote products
			derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
```
