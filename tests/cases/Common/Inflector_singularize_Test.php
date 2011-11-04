<?php

use Orm\Inflector;

/**
 * @covers Orm\Inflector::singularize
 */
class Inflector_singularize_Test extends TestCase
{
	protected function t($s, $p)
	{
		$this->assertSame($s, Inflector::singularize($p));
	}

	public function test()
	{
		$this->t('search', 'searches');
		$this->t('switch', 'switches');
		$this->t('fix', 'fixes');
		$this->t('box', 'boxes');
		$this->t('process', 'processes');
		$this->t('address', 'addresses');

		$this->t('query', 'queries');
		$this->t('ability', 'abilities');
		$this->t('agency', 'agencies');

		$this->t('half', 'halves');
		$this->t('wife', 'wives');
		$this->t('staff', 'staffs');
		$this->t('dwarf', 'dwarfs');
		$this->t('knife', 'knives');
		$this->t('life', 'lives');

		$this->t('basis', 'bases');
		$this->t('diagnosis', 'diagnoses');

		$this->t('datum', 'data');
		$this->t('medium', 'media');

		$this->t('man', 'men');
		$this->t('woman', 'women');
		$this->t('spokesman', 'spokesmen');

		$this->t('foo', 'foos');
		$this->t('fo', 'fos');
		$this->t('f', 'fs');
		$this->t('', 's');
		$this->t('foo', 'foo');

		$this->t('tum', 'ta');
	}
}
