<?php

require dirname(__FILE__) . '/base.php';

NetteTestHelpers::skip();

/**
 * @property-read int $id
 * @property string $name
 * @property OneToMany $emails
 * @OneToMany Email
 */
class User extends Entity
{
	private $mainEmail;

	public function __construct()
	{
		$this->emails = new OneToMany($this);
	}

	public function addEmail($email)
	{
		if (!($email instanceof Email))
		{
			$email = new Email($email);
		}
		return $this->emails->add($email);
	}

	public function removeEmail($email)
	{
		if (!($email instanceof Email))
		{
			$email = new Email($email);
		}
		return $this->emails->remove($email);
	}

	public function getEmails()
	{
		$emails = $this->getValue('emails');
		return $emails;
	}


	public function getMainEmail()
	{
		if ($this->mainEmail instanceof Email) return $this->mainEmail;
		$main = Factory::getRepository('email')->getByUserAndMain($this, true);
		if (!$main)
		{
			$main = Factory::getRepository('email')->findByUser($this)->fetch();
		}
		return $this->mainEmail = $main;
	}

	public function setMainEmail($email)
	{
		$this->mainEmail = $email->setUser($this)->setMain(true);
		return $this;
	}

}

class OneToMany extends Object
{

	private $one;
	private $many = array();
	private $deleted = array();

	public function __construct(Entity $one)
	{
		$this->one = $one;
	}

	public function add($e)
	{
		$e->{Factory::getName($this->one)} = $this->one;
		$this->many[] = $e;
		return $e;
	}

	public function remove($e)
	{
		$this->deleted[] = $e;
		return $e;
	}

	public function getIterator()
	{
		if ($this->deleted)
		{
			$result = array();
			foreach ($this->many as $m)
			{
				foreach ($this->deleted as $d)
				{
					if ($m->compare($d))
					{
						continue;
					}
				}
				$result[] = $m;
			}
			return new ArrayIterator($result);
		}
		return new ArrayIterator($this->many);
	}

}


/**
 * @property string $email
 * @property User $user
 * @property bool $main
 */
class Email extends Entity
{
	public function __construct($email)
	{
		$this->email = $email;
	}

	public function setEmail($email)
	{
		if (!ValidationHelper::isEmail($email))
		{
			throw new InvalidStateException('Ocekavan email: '.$email);
		}
		return $this->setValue('email', $email);
	}

	public function __toString()
	{
		return $this->email;
	}

	public function setUser(User $user)
	{
		$oldUser = $this->getValue('user', false);
		if ($oldUser AND $oldUser !== $user) $oldUser->emails->remove($this);
		$user->emails->add($this);
		return $this->setValue('user', $user);
	}

	/*public function setMain($main)
	{
		$this->setValue('main', $main)
		$user = $this->getValue('user', false);
		if ($user) $user->setMainEmail($this);
	}*/
}

$t = new User;

dump($t->getEmails() instanceof OneToMany, 'OneToMany');

$t->addEmail(new Email('a@a.cz'));
for ($i=500;$i--;)
{
	$t->addEmail(new Email($i.'@a.cz'));
}

$t->addEmail('b@b.cz');

dump($t->getMainEmail().'');

$t->removeEmail('a@a.cz');

dump($t->getMainEmail().'');

$t->setMainEmail('c@c.cz');

dump($t->getMainEmail().'');

$email = new Email('d@d.cz');
$t->addEmail($email->setMain(true));

dump($t->getMainEmail().'');

$t->setName('s');

__halt_compiler();
------EXPECT------
OneToMany: bool(TRUE)

string(%i%) "a@a.cz"

string(%i%) "b@b.cz"

string(%i%) "c@c.cz"

string(%i%) "d@d.cz"
