<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    GNU Lesser General Public License version 3 or later.
 */

namespace Windwalker\Renderer;

use Windwalker\Registry\Registry;

/**
 * Class AbstractRenderer
 *
 * @property-read  Registry  $config  Config data.
 *
 * @since 2.0
 */
abstract class AbstractRenderer implements RendererInterface
{
	/**
	 * Property paths.
	 *
	 * @var  \SplPriorityQueue
	 */
	protected $paths = null;

	/**
	 * Property config.
	 *
	 * @var  Registry
	 */
	protected $config = array();

	/**
	 * Class init.
	 *
	 * @param \SplPriorityQueue $paths
	 * @param array             $config
	 */
	public function __construct($paths = null, $config = array())
	{
		$this->setPaths($paths);

		$this->config = new Registry($this->config);

		$this->config->load($config);
	}

	/**
	 * Method to escape output.
	 *
	 * @param   string  $output  The output to escape.
	 *
	 * @return  string  The escaped output.
	 *
	 * @see     ViewInterface::escape()
	 * @since   2.0
	 */
	public function escape($output)
	{
		// Escape the output.
		return htmlspecialchars($output, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * finFile
	 *
	 * @param string $file
	 * @param string $ext
	 *
	 * @return  string
	 */
	public function findFile($file, $ext = '')
	{
		$paths = clone $this->getPaths();

		$file = str_replace('.', '/', $file);

		$ext = $ext ? '.' . trim($ext, '.') : '';

		foreach ($paths as $path)
		{
			$filePath = $path . '/' . $file . $ext;

			if (is_file($filePath))
			{
				return realpath($filePath);
			}
		}

		return null;
	}

	/**
	 * getPaths
	 *
	 * @return  \SplPriorityQueue
	 */
	public function getPaths()
	{
		return $this->paths;
	}

	/**
	 * setPaths
	 *
	 * @param   \SplPriorityQueue $paths
	 *
	 * @return  AbstractRenderer  Return self to support chaining.
	 */
	public function setPaths($paths)
	{
		if (!($paths instanceof \SplPriorityQueue))
		{
			$priority = new \SplPriorityQueue;

			foreach ((array) $paths as $i => $path)
			{
				$priority->insert($path, 100 - ($i * 10));
			}

			$paths = $priority;
		}

		$this->paths = $paths;

		return $this;
	}

	/**
	 * addPath
	 *
	 * @param string  $path
	 * @param integer $priority
	 *
	 * @return  static
	 */
	public function addPath($path, $priority = 100)
	{
		$this->paths->insert($path, $priority);

		return $this;
	}

	/**
	 * dumpPaths
	 *
	 * @return  array
	 */
	public function dumpPaths()
	{
		$paths = clone $this->paths;

		$return = array();

		foreach ($paths as $path)
		{
			$return[] = $path;
		}

		return $return;
	}

	/**
	 * __get
	 *
	 * @param string $name
	 *
	 * @return  mixed
	 */
	public function __get($name)
	{
		if ($name == 'config')
		{
			return $this->$name;
		}

		throw new \UnexpectedValueException('Property ' . $name . ' not extists.');
	}
}
