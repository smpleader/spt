<?php
/**
 * SPT software - Layout
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just base class for a  layout
 * 
 */

namespace SPT\Web\Layout;

use SPT\Web\Theme;
use SPT\Web\View;

class Base
{ 
    /**
    * Internal variable cache file path
    * @var string $_path
    */
    protected readonly string $__path;

    /**
    * Internal variable cache a token: plugin:type:path
    * @var string $_id
    */
    protected readonly string $__id;

    /**
    * View object
    * @var View $__view
    */
    protected View $__view; 

    /**
    * Avoid a loop in a layout
    * @var boolean $__locked
    */
    protected  bool $__locked = false;

    /**
    * Avoid a loop in a part
    * @var boolean $__lockedPart
    */
    protected  bool $__lockedPart = false;
    
    /**
     * Constructor
     * 
     * @param View   $view variable view
     * @param string   $id token, format plugin:type:path
     * @param string   $path path file 
     * 
     * @return void 
     */ 
    public function __construct(View $view, string $id, string $path)
    {
        if(!file_exists($path))
        {
            throw new \Exception('Can not create a layout from path '.$path);
        }
        
        $this->__path = $path;
        $this->__id = $id;
        $this->__view = $view;
    }

    /**
     * render content into string
     * 
     * @return string 
     */ 
    public function _render(string $layoutId = '', array $data = []): string
    {
        if($layoutId)
        {
            return $this->__view->render($layoutId, $data);
        }

        if($this->__locked)
        {
            return ''; // TODO: warning if DEBUG mode is ON
        }
        else
        {
            $this->__locked = true;
        }

        ob_start();
        include $this->__path;
        $content = ob_get_clean();

        $this->__locked = false;        
        return $content;
    }

    /**
     * render content based current layout parts
     * 
     * @return string 
     */ 
    public function part(string $subpath, array $data = []): string
    {
        $_tmp = pathinfo($this->__path);
        $_path = $_tmp['dirname']. '/'. str_replace('.', '/', $subpath);
        $_path = $this->__view->fileExists($_path);
        if(false == $_path)
        {
            throw new \Exception('Invalid part layout '.$subpath. ' with '. $this->__id);
        }

        if($this->__lockedPart)
        {
            return ''; // TODO: warning if DEBUG mode is ON
        }
        else
        {
            $this->__lockedPart = true;
        }

        ob_start();
        include $_path;
        $content = ob_get_clean();

        $this->__lockedPart = false;        
        return $content;
    }

    /**
     * return current token
     * 
     * @return string 
     */ 
    public function getId(): string
    {
        return $this->__id;
    }

    /**
     * echo content to response
     * 
     * @return void 
     */ 
    public function render(string $layoutId = '', array $data = []): void
    {
        echo $this->_render($layoutId);
    }

    /**
     * update data if new one
     * 
     * @return void 
     */ 
    public function update(array $data, bool $isMethod = false): void {}

    /**
     * get data from controller
     * 
     * @return mixed 
     */ 
    public function data( string | int $key, $default = null, ?string $format = null)
    {
        $_vars = $this->__view->getData();
        if(!isset($_vars[$key])) return $default;
        return $format ? Filter::$format($_vars[$key]) : $_vars[$key];
    } 
}