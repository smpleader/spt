<?php
/**
 * SPT software - Log
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A way to log some information for admin
 * 
 */

namespace SPT;

use SPT\Application\Instance AppInstance;

function spt_factory(string $key)
{
    if( null === AppInstance::$app )
    {
        die('Can not run outside the application instance.');
    }
    
    $key = strtolower($key);
    // 'user', 'query', 'config', 'router' ..
    return AppInstance::factory($key);
}

if( !function_exists('f') )
{
    function f(string $key)
    {
        return spt_factory($key);
    }
}
elseif( !function_exists('factory') )
{
    function factory(string $key)
    {
        return spt_factory($key);
    }
}

// a shortcut for internal usage
if( !function_exists('spt') )
{
    function spt(string $key)
    {
        return spt_factory($key);
    }
}
