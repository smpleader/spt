<?php
/**
 * SPT software - Layout
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just base class for   layouts
 * 
 */

namespace SPT\Web\Layout;

use SPT\Web\Theme;
use SPT\Support\View;

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
    * Theme info
    * @var Theme $theme
    */
    protected Theme $theme; 

    /**
    * Avoid a loop 
    * @var boolean $__locked
    */
    protected  bool $__locked = false;
    
    /**
     * Constructor
     * 
     * @param Theme   $theme variable theme
     * @param string   $id token, format plugin:type:path
     * @param string   $path path file 
     * 
     * @return void 
     */ 
    public function __construct(Theme $theme, string $id, string $path)
    {
        if(!file_exists($path))
        {
            throw new \Exception('Can not create a layout from path '.$path);
        }
        
        $this->__path = $path;
        $this->__id = $id;
        $this->theme = &$theme;
    }

    /**
     * render content into string
     * 
     * @return string 
     */ 
    public function _render(string $layoutId = '', array $data = []): string
    {
        if($layoutId && $layoutId !== $this->__id)
        {
            return View::render($layoutId, $data);
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
}