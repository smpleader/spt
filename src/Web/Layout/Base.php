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
    * @var string $__path
    */
    protected readonly string $__path;

    /**
    * Internal variable cache folder id
    * @var string $__sibling
    */
    protected readonly string $__sibling;

    /**
    * Internal variable cache a plugin id
    * @var string $__pid
    */
    protected readonly string $__pid;

    /**
    * Internal variable cache a layout token: theme:path
    * @var string $__token
    */
    protected readonly string $__token;

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
     * Constructor
     * 
     * @param View   $view variable view
     * @param string   $pluginid token, format plugin:path
     * @param string   $token token, format theme::path
     * @param string   $path path file 
     * 
     * @return void 
     */ 
    public function __construct(View $view, string $pluginid, string $token, string $path)
    {
        if(!file_exists($path))
        {
            throw new \Exception('Can not create a laypluginout from path '.$path);
        }
        
        $this->__path = $path;
        $this->__pid = $pluginid;
        $this->__view = $view;
        $this->__token = $token;

        // calculate sibling
        $dotPos = strrpos($token, '.');
        $this->__sibling =  false ===  $dotPos? '' : substr($token, 0, $dotPos);
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
     * return  layout ID
     * 
     * @return string 
     */ 
    public function getId(): string
    {
        return $this->__pid. ':'. $this->__token;
    }

    /**
     * return  layout token
     * 
     * @return string 
     */ 
    public function getToken(): string
    {
        return $this->__token;
    }

    /**
     * return  plugin ID
     * 
     * @return string 
     */ 
    public function getPluginId(): string
    {
        return $this->__pid;
    }

    /**
     * return sibling layout
     * 
     * @return string 
     */ 
    public function getPartToken(string $subpath): string
    {
        return empty($this->__sibling) ? $subpath : $this->__sibling. '.'. $subpath;
    }

    /**
     * echo content to response
     * 
     * @return void 
     */ 
    public function render(string $layoutId = '', array $data = []): void
    {
        echo $this->_render($layoutId, $data);
    }

    /**
     * render content based current layout parts
     * 
     * @return void 
     */ 
    public function part(string $subpath, array $data = []): void
    {
        $layoutId = $this->__pid. ':'. $this->getPartToken($subpath);
        $this->__view->render($layoutId, $data, false);
    }

    /**
     * update data if new one
     * 
     * @return void 
     */ 
    public function update(array $data, bool $isMethod = false): void {}
}