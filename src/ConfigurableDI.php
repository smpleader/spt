<?php
/**
 * SPT software - Configurable DI
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Load properties from defined array of key => classType
 * 
 */

namespace SPT;

abstract class ConfigurableDI 
{
    /**
     * Internal token string or array to recognize current context
     * @var mixed $context
     */
    protected $context;

    /**
     * Get context information
     * 
     * @return mixed
     */     
    public function getContext()
    {
        if( empty($this->context) )
        {
            $arr = explode('\\', static::class);
            $this->context = end($arr);
        }
        return $this->context;
    }

    /**
     * Assign properties based defined "mutable fileds" array
     *
     * @param array   $options Variable need to set into properties
     * 
     * @return void
     */ 
    public function init(array $options)
    {
        foreach($this->getMutableFields() as $key => $type)
        {
            $loaded = false;
            if(  array_key_exists( $key, $options ) )
            {
                if( $type )
                {
                    if( is_a($options[$key], $type) )
                    {
                        $loaded = true;
                        $this->$key = $options[$key];
                    }
                }
                else
                {
                    $loaded = true;
                    $this->$key = $options[$key];
                }
            }

            if ( !$loaded )
            {
                throw new \Exception( static::class. ' requires '. $key. ' type of '. $type.  ' for its operations', 500);
            }
        }
    }

    /**
     * Return defined "mutable fileds" array
     * 
     * @return array
     */ 
    abstract protected function getMutableFields(): array;
}
