<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004, 2011 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\Web
 */



/**
 * User authentication and authorization.
 *
 * @author     David Grudl
 */
interface IUser
{

	/**
	 * Conducts the authentication process.
	 * @param  mixed optional parameter (e.g. username)
	 * @param  mixed optional parameter (e.g. password)
	 * @return void
	 * @throws AuthenticationException if authentication was not successful
	 */
	function login();

	/**
	 * Logs out the user from the current session.
	 * @return void
	 */
	function logout($clearIdentity = FALSE);

	/**
	 * Is this user authenticated?
	 * @return bool
	 */
	function isLoggedIn();

	/**
	 * Returns current user identity, if any.
	 * @return IIdentity
	 */
	function getIdentity();

	/**
	 * Sets authentication handler.
	 * @param  IAuthenticator
	 * @return void
	 */
	function setAuthenticationHandler(IAuthenticator $handler);

	/**
	 * Returns authentication handler.
	 * @return IAuthenticator
	 */
	function getAuthenticationHandler();

	/**
	 * Changes namespace; allows more users to share a session.
	 * @param  string
	 * @return void
	 */
	function setNamespace($namespace);

	/**
	 * Returns current namespace.
	 * @return string
	 */
	function getNamespace();

	/**
	 * Returns a list of roles that a user has been granted.
	 * @return array
	 */
	function getRoles();

	/**
	 * Is a user in the specified role?
	 * @param  string
	 * @return bool
	 */
	function isInRole($role);

	/**
	 * Has a user access to the Resource?
	 * @return bool
	 */
	function isAllowed();

	/**
	 * Sets authorization handler.
	 * @param  IAuthorizator
	 * @return void
	 */
	function setAuthorizationHandler(IAuthorizator $handler);

	/**
	 * Returns current authorization handler.
	 * @return IAuthorizator
	 */
	function getAuthorizationHandler();

}
