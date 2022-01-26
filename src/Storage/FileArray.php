<?php
/**
 * SPT software - Storage File
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Storage File
 * 
 */

namespace SPT\Storage;

class FileArray extends File
{
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->data = (array) require_once $path;
    }

    public function print($data = null, $deep = 0)
    {
        if( null === $data ) $data = $this->data;

        if(count($data))
        {
            $tabParent =  str_repeat("\t", $deep);

            $str = "$tabParent[\n";

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

            $str .= "$tabParent]";

            return $str;
        }
        else
        {
            return $deep > 0 ? '' : '[]';
        }
    }

    public function toFile()
    {
        file_put_contents($this->path, $this->print());
    }
}