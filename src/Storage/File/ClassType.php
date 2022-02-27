<?php
/**
 * SPT software - Storage File
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: File Export a Class
 * 
 */

namespace SPT\Storage\File;

class ClassType extends Base
{
    public function parse(string $path)
    {
        $className = basename( $path, '.php');
        try
        {
            include $path;
            if(class_exists($className))
            {
                $sth = new $className;
                $this->_data[$className] = $sth;
            }
        }
        catch (\Exception $e) 
        {
            $this->response('Caught Exception: '.  $e->getMessage(), 500);
        }
    }

    public function __set(string $name, mixed $value): void
    {
        return;
        // TODO: consider to add property into object 
    }

    public function __get($name)
	{
		if (isset($this->_data[$name]))
		{
			return $this->_data[$name];
		}

        throw new \Exception('Unknown Storage Property '.$name);
	}

    public function print($data = null, $deep = 0)
    {
        if( null === $data ) $data = $this->_data;

        if(count($data))
        {
            $tabParent =  str_repeat("\t", $deep);

            $str = "$tabParent\{\n";

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

            $str .= "$tabParent\}";

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