<?php

namespace Orm\Builder;

use Exception;
use ApiGen\Config;

class ApiConfig extends Config
{

	/** @var array */
	private $config = array(
		'config' => '',
		# Source file or directory to parse
		'source' => array(),
		# Directory where to save the generated documentation
		'destination' => '',
		# Mask to exclude file or directory from processing
		'exclude' => array(),
		# Don't generate documentation for classes from file or directory with this mask
		'skipDocPath' => array(),
		# Don't generate documentation for classes with this name prefix
		'skipDocPrefix' => array(),
		# Main project name prefix
		'main' => '',
		# Title of generated documentation
		'title' => 'Orm',
		# Documentation base URL
		'baseUrl' => '',
		# Google Custom Search ID
		'googleCseId' => '',
		# Google Custom Search label
		'googleCseLabel' => '',
		# Google Analytics tracking code
		'googleAnalytics' => '',
		# Template config file
		'templateConfig' => '',
		# List of allowed HTML tags in documentation
		'allowedHtml' => array('b', 'i', 'a', 'ul', 'ol', 'li', 'p', 'br', 'var', 'samp', 'kbd', 'tt'),
		# Generate documentation for methods and properties with given access level
		'accessLevels' => array('public', 'protected'),
		# Generate documentation for elements marked as internal and display internal documentation parts
		'internal' => false,
		# Generate documentation for PHP internal classes
		'php' => true,
		# Generate tree view of classes, interfaces and exceptions
		'tree' => true,
		# Generate documentation for deprecated classes, methods, properties and constants
		'deprecated' => false,
		# Generate documentation of tasks
		'todo' => false,
		# Generate highlighted source code files
		'sourceCode' => true,
		# Save a list of undocumented classes, methods, properties and constants into a file
		'undocumented' => 'todo',
		# Wipe out the destination directory first
		'wipeout' => true,
		# Don't display scaning and generating messages
		'quiet' => true,
		# Display progressbars
		'progressbar' => false,
		# Use colors
		'colors' => false,
		# Display additional information in case of an error
		'debug' => true
	);

	/**
	 * Initializes configuration.
	 */
	public function __construct($source, $destination)
	{
		$this->config['source'] = $source;
		$this->config['destination'] = $destination;
		$this->config['templateConfig'] = __DIR__ . '/../libs/ApiGen/templates/default/config.neon';

		$cr = new \ReflectionProperty('ApiGen\Config', 'config');
		$cr->setAccessible(true);
		$cr->setValue($this, $this->config);

		parent::parse();

		$this->config = $cr->getValue($this);
	}

	/**
	 * Parses options and configuration.
	 *
	 * @return \ApiGen\Config
	 * @throws \ApiGen\Exception If something in config is wrong
	 */
	public function parse()
	{
		throw new Exception;
	}

	/**
	 * Checks if a configuration option exists.
	 * @param string $name Option name
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->config[$name]);
	}

	/**
	 * Returns a configuration option value.
	 * @param string $name Option name
	 * @return bool
	 */
	public function __get($name)
	{
		return isset($this->config[$name]) ? $this->config[$name] : NULL;
	}

}
