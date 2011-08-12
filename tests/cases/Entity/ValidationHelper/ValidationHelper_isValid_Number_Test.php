<?php

use Orm\ValidationHelper;

/**
 * @covers Orm\ValidationHelper::isValid
 */
class ValidationHelper_isValid_Number_Test extends ValidationHelper_isValid_Base
{

	public function testFloat()
	{
		$this->type = 'float';
		$this->t('1 057 000,055 987', true, 1057000.055987);
	}

	public function testInt()
	{
		$this->type = 'int';
		$this->t('1 057 000,055 987', true, 1057000);
	}

}
