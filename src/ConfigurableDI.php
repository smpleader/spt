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
    protected $context;
    public function getContext()
    {
        return empty($this->context) ? static::class : $this->context;
    }

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

    abstract protected function getMutableFields(): array;
}
