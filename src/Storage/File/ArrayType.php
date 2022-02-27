<?php
/**
 * SPT software - Storage File
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: File export Array
 * 
 */

namespace SPT\Storage\File;

class ArrayType extends Base
{
    public function import(string $path)
    {
        if(!in_array($path, $this->_paths))
        {
            $this->_paths[] = $path;
        }

        $this->parse($path); 
    }

    public function parse(string $path)
    {
        $arr = (array) require_once $path;
        $this->_data = array_merge($this->_data, $arr);
    }

    public function print($data = null, $deep = 0)
    {
        if( null === $data ) $data = $this->_data;

        if(count($data))
        {
            $tabParent =  str_repeat("\t", $deep);

            $str = "$tabParent\[\n";

            $tab =  str_repeat("\t", $deep+1);
            
            foreach($data as $key=>$value)
            {
                $str .= $tab;

                if(!is_numeric($key))
                {
                    $str .= "$tab'". $key. "' => ";
                }

                if(is_array($value))
                {
                    $str .= $this->print($value, $deep+1);
                }
                else
                {
                    $str .= "$tab'". str_replace("'", "\\'", $value);
                }

                $str .= "', \n";
            }

            $str .= "$tabParent\]";

            return $str;
        }
        else
        {
            return $deep > 0 ? '' : '[]';
        }
    }

    public function toFile(string $path)
    {
        file_put_contents($path, $this->print());
    }
}