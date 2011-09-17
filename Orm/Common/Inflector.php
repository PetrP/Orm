<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Inflection / modification of a word to express different grammatical categories.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Common
 */
class Inflector
{

	/**
	 * Singular to plural
	 * @param string
	 * @return string
	 */
	public static function pluralize($singular)
	{
		static $rules = array(
			'sis' => 'ses', // basis
			'tum' => 'ta', // datum
			'ium' => 'ia', // medium
			'man' => 'men',
			'ch' => 'ches', // search
			'ss' => 'sses', // address
			'sh' => 'shes', // switch
			'fe' => 'ves', // knife
			'lf' => 'lves', // half
			'by' => 'bies', 'cy' => 'cies', 'dy' => 'dies', 'fy' => 'fies', 'gy' => 'gies', // agency
			'hy' => 'hies', 'jy' => 'jies', 'ky' => 'kies', 'ly' => 'lies', 'my' => 'mies',
			'ny' => 'nies', 'py' => 'pies', 'qy' => 'qies', 'ry' => 'ries', 'sy' => 'sies', // query
			'ty' => 'ties', 'vy' => 'vies', 'wy' => 'wies', 'xy' => 'xies', 'zy' => 'zies', // ability
			'x' => 'xes', // fix
		);

		$end = strtolower(substr($singular, $len = -3));
		do {
			if (isset($rules[$end]))
			{
				return substr_replace($singular, $rules[$end], $len);
			}
			$end = substr($end, ++$len);
		} while ($len);
		return $singular . 's';
	}

	/**
	 * Plural to singular
	 * @param string
	 * @return string
	 */
	public static function singularize($plural)
	{
		static $rules = array(
			'ches' => 'ch', // search
			'sses' => 'ss', // address
			'shes' => 'sh', // switch
			'lves' => 'lf', // half
			'ses' => 'sis', // basis
			'men' => 'man',
			'ves' => 'fe', // knife
			'xes' => 'x', // fix
			'ies' => 'y', // agency
			'ta' => 'tum', // datum
			'ia' => 'ium', // medium
			's' => '',
		);

		$end = strtolower(substr($plural, $len = -4));
		do {
			if (isset($rules[$end]))
			{
				return substr_replace($plural, $rules[$end], $len);
			}
			$end = substr($end, ++$len);
		} while ($len);
		return $plural;
	}
}
