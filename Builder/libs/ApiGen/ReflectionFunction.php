<?php

/**
 * ApiGen 2.1.0 - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen;

/**
 * Function reflection envelope.
 *
 * Alters TokenReflection\IReflectionFunction functionality for ApiGen.
 *
 * @author Jaroslav Hanslík
 */
class ReflectionFunction extends ReflectionFunctionBase
{
	/**
	 * Returns if the function should be documented.
	 *
	 * @return boolean
	 */
	public function isDocumented()
	{
		if (null === $this->isDocumented && parent::isDocumented()) {
			foreach ($this->config->skipDocPath as $mask) {
				if (fnmatch($mask, $this->reflection->getFilename(), FNM_NOESCAPE)) {
					$this->isDocumented = false;
					break;
				}
			}
			if (true === $this->isDocumented) {
				foreach ($this->config->skipDocPrefix as $prefix) {
					if (0 === strpos($this->reflection->getName(), $prefix)) {
						$this->isDocumented = false;
						break;
					}
				}
			}
		}

		return $this->isDocumented;
	}
}