<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    GNU Lesser General Public License version 3 or later.
 */

namespace Windwalker\Renderer;

/**
 * The AbstractEngineRenderer class.
 * 
 * @since  2.0
 */
abstract class AbstractEngineRenderer extends AbstractRenderer
{
	/**
	 * Property engine.
	 *
	 * @var  object
	 */
	protected $engine = null;

	/**
	 * Method to get property Engine
	 *
	 * @param   boolean $new
	 *
	 * @return  object
	 */
	abstract public function getEngine($new = false);

	/**
	 * Method to set property engine
	 *
	 * @param   object $engine
	 *
	 * @return  static  Return self to support chaining.
	 */
	abstract public function setEngine($engine);
}
