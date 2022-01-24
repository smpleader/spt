<?php
/**
 * SPT software - Request Header
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Request by Header
 * 
 */

namespace SPT\Request;

class Header extends Base
{
    public function __construct(array $source = null)
    {
        if (function_exists('getallheaders'))
        {
            $this->data = getallheaders();
        }
        // nginx ?
        elseif(isset($_SERVER))
        { 
            $headers = [];
            foreach ($_SERVER as $name => $value)
            {
                if (substr($name, 0, 5) == 'HTTP_')
                {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            $this->data = $headers;
        }
        // this is cli ?
        else
        {
            throw new Exception('Application is running in environment without header request.');
        } 
    }
}