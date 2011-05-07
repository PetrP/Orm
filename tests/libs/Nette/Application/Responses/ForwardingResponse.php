<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004, 2011 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\Application
 */



/**
 * Forwards to new request.
 *
 * @author     David Grudl
 */
class ForwardingResponse extends Object implements IPresenterResponse
{
	/** @var PresenterRequest */
	private $request;



	/**
	 * @param  PresenterRequest  new request
	 */
	public function __construct(PresenterRequest $request)
	{
		$this->request = $request;
	}



	/**
	 * @return PresenterRequest
	 */
	final public function getRequest()
	{
		return $this->request;
	}



	/**
	 * Sends response to output.
	 * @return void
	 */
	public function send(IHttpRequest $httpRequest, IHttpResponse $httpResponse)
	{
	}

}
