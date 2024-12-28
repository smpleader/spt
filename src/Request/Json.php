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

class Json extends Base
{
	protected $_raw;
    
    public function __construct(?array $source = null)
    {   
		if (is_null($source))
		{
			$this->_raw = file_get_contents('php://input');
			$this->data = json_decode($this->_raw, true);

			if (!is_array($this->data))
			{
				$this->data = array();
			}
		}
		else
		{
			$this->data = &$source;
		}   
    }
}
