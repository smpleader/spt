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
                if (substr($name, 0, 5) == 'HTTP_' || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH'))
                {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            $this->data = $headers;
        }
        // this is cli ?
        else
        {
            throw new \Exception('Application is running in environment without header request.');
        } 
    }

    public function getRequestMethod()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // If it's a HEAD request override it to being GET and prevent any output, as per HTTP Specification
        // @url http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD')
        {
            ob_start();
            $method = 'GET';
        }

        // If it's a POST request, check for a method override header
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $headers = $this->getRequestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], ['PUT', 'DELETE', 'PATCH']))
            {
                $method = $headers['X-HTTP-Method-Override'];
            }

            // well, support $_POST['_method']
            if(isset($_POST['_method']) && in_array($_POST['_method'], ['PUT', 'DELETE', 'PATCH']))
            {
                $method = $_POST['_method'];
            }
        }

        return strtolower($method);
    }
}