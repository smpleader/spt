<?php
/**
 * SPT software - Request JSON
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Request by JSON body
 * 
 */

namespace SPT\Request;

use SPT\Support\Filter;

class Base
{
	protected $data = array();
	protected $inputs = array();
	protected static $loaded = false;

    public function __construct(array $source = null)
    {   
		if ($source === null)
		{
			$this->data = &$_REQUEST;
		}
		else
		{
			$this->data = $source;
		}
    }

	public function set($name, $value)
	{
		$this->data[$name] = $value;
	}

    public function get($name, $default = null, $filter = 'cmd')
	{
		if (isset($this->data[$name]))
		{
			return Filter::$filter($this->data[$name]) ;
        }

		return $default;
	}

    public function getMethod()
	{
		return isset($_SERVER) ? null : strtoupper($_SERVER['REQUEST_METHOD']);
	}

    public function __get($name)
	{
		if (isset($this->inputs[$name]))
		{
			return $this->inputs[$name];
		}

		$className = '\\SPT\\Request\\' . ucfirst($name);

		if (class_exists($className))
		{
			$this->inputs[$name] = new $className(null);

			return $this->inputs[$name];
		}

        throw new Exception('Unknown Request Object '.$name);
	}

}
