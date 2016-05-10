<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    GNU Lesser General Public License version 3 or later.
 */

namespace Windwalker\Registry;

if (!interface_exists('JsonSerializable'))
{
	include_once __DIR__ . '/Compat/JsonSerializable.php';
}

/**
 * Registry class
 *
 * @since  2.0
 */
class Registry implements \JsonSerializable, \ArrayAccess, \IteratorAggregate, \Countable
{
	/**
	 * Property separator.
	 *
	 * @var  string
	 */
	protected $separator = '.';

	/**
	 * Registry data store.
	 *
	 * @var    array
	 * @since  2.0
	 */
	protected $data = array();

	/**
	 * Property ignoreValues.
	 *
	 * @var  array
	 */
	protected $ignoreValues = array(null);

	/**
	 * Constructor
	 *
	 * @param   mixed   $data   The data to bind to the new Registry object.
	 * @param   string  $format The format of input, only work when first argument is string.
	 *
	 * @since   2.0
	 */
	public function __construct($data = null, $format = Format::JSON)
	{
		// Optionally load supplied data.
		if (is_array($data) || is_object($data))
		{
			$this->bindData($this->data, $data);
		}
		elseif (!empty($data) && is_string($data))
		{
			$this->loadString($data, $format);
		}
	}

	/**
	 * Magic function to clone the registry object.
	 *
	 * @return  Registry
	 *
	 * @since   2.0
	 */
	public function __clone()
	{
		$this->data = unserialize(serialize($this->data));
	}

	/**
	 * Magic function to render this object as a string using default args of toString method.
	 *
	 * @return  string
	 *
	 * @since   2.0
	 */
	public function __toString()
	{
		try
		{
			return $this->toString();
		}
		catch (\Exception $e)
		{
			return trigger_error((string) $e, E_USER_ERROR);
		}
	}

	/**
	 * Implementation for the JsonSerializable interface.
	 * Allows us to pass Registry objects to json_encode.
	 *
	 * @return  array
	 *
	 * @since   2.0
	 * @note    The interface is only present in PHP 5.4 and up.
	 */
	public function jsonSerialize()
	{
		return $this->data;
	}

	/**
	 * Sets a default value if not already assigned.
	 *
	 * @param   string  $path   The name of the parameter.
	 * @param   mixed   $value  An optional value for the parameter.
	 *
	 * @return  static  Return self to support chaining.
	 *
	 * @since   2.0
	 */
	public function def($path, $value = '')
	{
		$value = $this->get($path, $value);
		$this->set($path, $value);

		return $this;
	}

	/**
	 * Check if a registry path exists.
	 *
	 * @param   string  $path  Registry path (e.g. foo.content.showauthor)
	 *
	 * @return  boolean
	 *
	 * @since   2.0
	 */
	public function exists($path)
	{
		return !is_null($this->get($path));
	}

	/**
	 * Get a registry value.
	 *
	 * @param   string  $path       Registry path (e.g. foo.content.showauthor)
	 * @param   mixed   $default    Optional default value, returned if the internal value is null.
	 *
	 * @return  mixed  Value of entry or null
	 *
	 * @since   2.0
	 */
	public function get($path, $default = null)
	{
		$result = RegistryHelper::getByPath($this->data, $path, $this->separator);

		return !is_null($result) ? $result : $default;
	}

	/**
	 * Reset all data.
	 *
	 * @return  static
	 */
	public function reset()
	{
		$this->data = array();

		return $this;
	}

	/**
	 * Clear all data.
	 *
	 * @return  static
	 *
	 * @deprecated  3.0
	 */
	public function clear()
	{
		return $this->reset();
	}

	/**
	 * Load a associative array of values into the default namespace
	 *
	 * @param   array  $array  Associative array of value to load
	 *
	 * @return  static  Return this object to support chaining.
	 *
	 * @since   2.0
	 * @deprecated  3.0  Use load() instead.
	 */
	public function loadArray($array)
	{
		$this->bindData($this->data, $array, false);

		return $this;
	}

	/**
	 * Load the public variables of the object into the default namespace.
	 *
	 * @param   object  $object  The object holding the publics to load
	 *
	 * @return  static  Return this object to support chaining.
	 *
	 * @since   2.0
	 * @deprecated  3.0  Use load() instead.
	 */
	public function loadObject($object)
	{
		$this->bindData($this->data, $object, false);

		return $this;
	}

	/**
	 * Load an array or object of values into the default namespace
	 *
	 * @param  array|object  $data  The value to load into registry.
	 * @param  boolean       $raw   Set to false that we will convert all object to array.
	 *
	 * @return static Return this object to support chaining.
	 */
	public function load($data, $raw = false)
	{
		$this->bindData($this->data, $data, $raw);

		return $this;
	}

	/**
	 * Load the contents of a file into the registry
	 *
	 * @param   string  $file     Path to file to load
	 * @param   string  $format   Format of the file [optional: defaults to JSON]
	 * @param   array   $options  Options used by the formatter
	 *
	 * @return  static  Return this object to support chaining.
	 *
	 * @since   2.0
	 */
	public function loadFile($file, $format = Format::JSON, $options = array())
	{
		$this->load(RegistryHelper::loadFile($file, $format, $options), false);

		return $this;
	}

	/**
	 * Load a string into the registry
	 *
	 * @param   string  $data     String to load into the registry
	 * @param   string  $format   Format of the string
	 * @param   array   $options  Options used by the formatter
	 *
	 * @return  static  Return this object to support chaining.
	 *
	 * @since   2.0
	 */
	public function loadString($data, $format = Format::JSON, $options = array())
	{
		$this->load(RegistryHelper::loadString($data, $format, $options), false);

		return $this;
	}

	/**
	 * Merge a structure data into this object.
	 *
	 * @param   Registry|mixed  $source  Source structure data to merge.
	 * @param   boolean         $raw     Set to false to convert all object to array.
	 *
	 * @return  static  Return this object to support chaining.
	 *
	 * @since   2.0
	 */
	public function merge($source, $raw = false)
	{
		if ($source instanceof Registry)
		{
			$source = $source->getRaw();
		}

		$this->bindData($this->data, $source, $raw);

		return $this;
	}

	/**
	 * Merge a structure data to a node.
	 *
	 * @param   string    $path    The path to merge as root.
	 * @param   Registry  $source  Source structure data to merge.
	 * @param   boolean   $raw     Set to false to convert all object to array.
	 *
	 * @return  static
	 */
	public function mergeTo($path, $source, $raw = false)
	{
		$nodes = RegistryHelper::getPathNodes($path);

		$data = array();

		$tmp =& $data;

		foreach ($nodes as $node)
		{
			$tmp[$node] = array();

			$tmp =& $tmp[$node];
		}

		if ($source instanceof Registry)
		{
			$source = $source->getRaw();
		}

		$tmp = $source;

		$this->bindData($this->data, $data, $raw);

		return $this;
	}

	/**
	 * extract
	 *
	 * @param string $path
	 *
	 * @return  static
	 */
	public function extract($path)
	{
		return new static($this->get($path));
	}

	/**
	 * getRaw
	 *
	 * @return  \stdClass
	 */
	public function getRaw()
	{
		return $this->data;
	}

	/**
	 * Checks whether an offset exists in the iterator.
	 *
	 * @param   mixed  $offset  The array offset.
	 *
	 * @return  boolean  True if the offset exists, false otherwise.
	 *
	 * @since   2.0
	 */
	public function offsetExists($offset)
	{
		return (boolean) ($this->get($offset) !== null);
	}

	/**
	 * Gets an offset in the iterator.
	 *
	 * @param   mixed  $offset  The array offset.
	 *
	 * @return  mixed  The array value if it exists, null otherwise.
	 *
	 * @since   2.0
	 */
	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	/**
	 * Sets an offset in the iterator.
	 *
	 * @param   mixed  $offset  The array offset.
	 * @param   mixed  $value   The array value.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	/**
	 * Unsets an offset in the iterator.
	 *
	 * @param   mixed  $offset  The array offset.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	public function offsetUnset($offset)
	{
		$this->set($offset, null);
	}

	/**
	 * Set a registry value and convert object to array.
	 *
	 * @param   string  $path   Registry Path (e.g. foo.content.showauthor)
	 * @param   mixed   $value  Value of entry.
	 *
	 * @return  static  Return self to support chaining.
	 *
	 * @since   2.0
	 */
	public function set($path, $value)
	{
		if (is_array($value) || is_object($value))
		{
			$value = RegistryHelper::toArray($value, true);
		}

		RegistryHelper::setByPath($this->data, $path, $value, $this->separator);

		return $this;
	}

	/**
	 * Set a registry value.
	 *
	 * @param   string  $path   Registry Path (e.g. foo.content.showauthor)
	 * @param   mixed   $value  Value of entry.
	 *
	 * @return  static  Return self to support chaining.
	 *
	 * @since   2.1
	 */
	public function setRaw($path, $value)
	{
		RegistryHelper::setByPath($this->data, $path, $value, $this->separator);

		return $this;
	}

	/**
	 * Transforms a namespace to an array
	 *
	 * @return  array  An associative array holding the namespace data
	 *
	 * @since   2.0
	 */
	public function toArray()
	{
		return (array) $this->asArray($this->data);
	}

	/**
	 * Transforms a namespace to an object
	 *
	 * @param   string  $class  The class of object.
	 *
	 * @return  object   An an object holding the namespace data
	 *
	 * @since   2.0
	 */
	public function toObject($class = 'stdClass')
	{
		return RegistryHelper::toObject($this->data, $class);
	}

	/**
	 * Get a namespace in a given string format
	 *
	 * @param   string  $format   Format to return the string in
	 * @param   mixed   $options  Parameters used by the formatter, see formatters for more info
	 *
	 * @return  string   Namespace in string format
	 *
	 * @since   2.0
	 */
	public function toString($format = Format::JSON, $options = array())
	{
		return RegistryHelper::toString($this->data, $format, $options);
	}

	/**
	 * Method to recursively bind data to a parent object.
	 *
	 * @param   array    $parent  The parent object on which to attach the data values.
	 * @param   mixed    $data    An array or object of data to bind to the parent object.
	 * @param   boolean  $raw     Set to false to convert all object to array.
	 *
	 * @return  void
	 */
	protected function bindData(&$parent, $data, $raw = false)
	{
		// Ensure the input data is an array.
		if (!$raw)
		{
			$data = RegistryHelper::toArray($data, true);
		}

		foreach ($data as $key => $value)
		{
			if (in_array($value, $this->ignoreValues, true))
			{
				continue;
			}

			if (is_array($value))
			{
				if (!isset($parent[$key]))
				{
					$parent[$key] = array();
				}

				$this->bindData($parent[$key], $value);
			}
			else
			{
				$parent[$key] = $value;
			}
		}
	}

	/**
	 * Method to recursively convert an object of data to an array.
	 *
	 * @param   mixed  $data  An object of data to return as an array.
	 *
	 * @return  array  Array representation of the input object.
	 *
	 * @since   2.0
	 */
	protected function asArray($data)
	{
		$array = array();

		if (is_object($data))
		{
			$data = get_object_vars($data);
		}

		foreach ($data as $k => $v)
		{
			if (is_object($v) || is_array($v))
			{
				$array[$k] = $this->asArray($v);
			}
			else
			{
				$array[$k] = $v;
			}
		}

		return $array;
	}

	/**
	 * Dump to on dimension array.
	 *
	 * @param string $separator The key separator.
	 *
	 * @return  string[] Dumped array.
	 */
	public function flatten($separator = '.')
	{
		return RegistryHelper::flatten($this->data, $separator);
	}

	/**
	 * Method to get property Separator
	 *
	 * @return  string
	 *
	 * @since   2.1
	 */
	public function getSeparator()
	{
		return $this->separator;
	}

	/**
	 * Method to set property separator
	 *
	 * @param   string $separator
	 *
	 * @return  static  Return self to support chaining.
	 *
	 * @since   2.1
	 */
	public function setSeparator($separator)
	{
		$this->separator = $separator;

		return $this;
	}

	/**
	 * Push value to a path in registry
	 *
	 * @param   string  $path   Parent registry Path (e.g. windwalker.content.showauthor)
	 * @param   mixed   $value  Value of entry, one or more elements.
	 *
	 * @return  integer  the new number of elements in the array.
	 *
	 * @since   2.1
	 */
	public function push($path, $value)
	{
		$node = $this->get($path);

		if (!$node)
		{
			$node = array();
		}
		elseif (is_object($node))
		{
			$node = get_object_vars($node);
		}

		if (!is_array($node))
		{
			throw new \UnexpectedValueException(sprintf('The value at path: %s should be object or array but is %s.', $path, gettype($node)));
		}

		$args = func_get_args();

		if (count($args) <= 2)
		{
			$num = array_push($node, $value);
		}
		else
		{
			$args[0] = &$node;

			$num = call_user_func_array('array_push', $args);
		}

		$this->set($path, $node);

		return $num;
	}

	/**
	 * Prepend value to a path in registry.
	 *
	 * @param   string  $path   Parent registry Path (e.g. windwalker.content.showauthor)
	 * @param   mixed   $value  Value of entry, one or more elements.
	 *
	 * @return  integer  the new number of elements in the array.
	 *
	 * @since   2.1
	 */
	public function unshift($path, $value)
	{
		$node = $this->get($path);

		if (!$node)
		{
			$node = array();
		}
		elseif (is_object($node))
		{
			$node = get_object_vars($node);
		}

		if (!is_array($node))
		{
			throw new \UnexpectedValueException(sprintf('The value at path: %s should be object or array but is %s.', $path, gettype($node)));
		}

		$args = func_get_args();

		if (count($args) <= 2)
		{
			$key = array_unshift($node, $value);
		}
		else
		{
			$args[0] = &$node;

			$key = call_user_func_array('array_unshift', $args);
		}

		$this->set($path, $node);

		return $key;
	}

	/**
	 * To remove first element from the path of this registry.
	 *
	 * @param   string  $path  The registry path.
	 *
	 * @return  mixed  The shifted value, or null if array is empty.
	 */
	public function shift($path)
	{
		$node = $this->get($path);

		if (is_object($node))
		{
			$node = get_object_vars($node);
		}

		if (!is_array($node))
		{
			throw new \UnexpectedValueException(sprintf('The value at path: %s should be object or array but is %s.', $path, gettype($node)));
		}

		$value = array_shift($node);

		$this->set($path, $node);

		return $value;
	}

	/**
	 * To remove last element from the path of this registry.
	 *
	 * @param   string  $path  The registry path.
	 *
	 * @return  mixed  The shifted value, or &null; if array is empty.
	 */
	public function pop($path)
	{
		$node = $this->get($path);

		if (is_object($node))
		{
			$node = get_object_vars($node);
		}

		if (!is_array($node))
		{
			throw new \UnexpectedValueException(sprintf('The value at path: %s should be object or array but is %s.', $path, gettype($node)));
		}

		$value = array_pop($node);

		$this->set($path, $node);

		return $value;
	}

	/**
	 * Gets this object represented as an RecursiveArrayIterator.
	 *
	 * This allows the data properties to be accessed via a foreach statement.
	 *
	 * You can wrap this iterator by RecursiveIteratorIterator that will support recursive foreach.
	 * Example: `foreach (new \RecursiveIteratorIterator($registry) as $value)`
	 *
	 * @return  \RecursiveArrayIterator  This object represented as an RecursiveArrayIterator.
	 *
	 * @see     IteratorAggregate::getIterator()
	 * @since   2.1
	 */
	public function getIterator()
	{
		return new \RecursiveArrayIterator($this->data);
	}

	/**
	 * Count elements of the data object
	 *
	 * @return  integer  The custom count as an integer.
	 *
	 * @link    http://php.net/manual/en/countable.count.php
	 * @since   2.1
	 */
	public function count()
	{
		return count($this->data);
	}

	/**
	 * Method to get property IgnoreValues
	 *
	 * @return  array
	 */
	public function getIgnoreValues()
	{
		return $this->ignoreValues;
	}

	/**
	 * Method to set property ignoreValues
	 *
	 * @param   array $ignoreValues
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setIgnoreValues($ignoreValues)
	{
		$this->ignoreValues = (array) $ignoreValues;

		return $this;
	}
}
