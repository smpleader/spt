<?php
/**
 * SPT software - Middleware loader
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: How we load the chain 
 * 
 */

namespace SPT\Middleware;

use SPT\App\Instance as AppIns;

class Loader
{
    protected $script;
    protected $namespace;
    protected $func;

    public function __construct( $path = '', $func = 'test' )
    {
        $this->func = $func;
        $this->namespace = AppIns::main()->getName($path);
    }

    public function ready()
    {
        return $this->script instanceof Script;
    }

    public function prepare(array $loaderList)
    {
        foreach($loaderList as $name)
        {
            $className = $this->namespace.'\\'.$name;
            if(class_exists($className) && is_subclass_of($className, '\SPT\Middleware\Script'))
            {
                $clss = new $className;
                if( null === $this->script )
                {
                    $this->script = $clss; 
                }
                else
                {
                    $this->script->linkWith($clss); 
                }
            }
        }
    }

    /**
     *      true => processed successfully
     *      false => something goes wrong
     *      other => log info
     */
    public function execute(array $params, string $fnc = '' )
    {
        if( empty($fnc) ) $fnc = $this->func;
        if( method_exists($this->script, $fnc))
        {
            return call_user_func_array([$this->script, $fnc],  $params);
        }
        
        return true;
    }
}