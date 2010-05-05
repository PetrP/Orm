<?php

require dirname(__FILE__) . '/base.php';

/**
 * @property string $email
 */
class Test extends Entity
{
	public function setEmail($email)
	{
		if (!ValidationHelper::isEmail($email))
		{
			throw new InvalidStateException('Ocekavan email: '.$email);
		}
		return $this->setValue('email', $email);
	}
}


$t = new Test;

try {
	$t->setEmail('asdasdasd');
} catch (Exception $e) { dump($e); }

$t->setEmail('asdasdasd@asdasd.cz');

dump($t->email);

__halt_compiler();
------EXPECT------
Exception InvalidStateException: Ocekavan email: asdasdasd

string(19) "asdasdasd@asdasd.cz"