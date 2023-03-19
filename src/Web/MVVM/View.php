<?php
/**
 * SPT software - View
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core view
 * 
 */

namespace SPT\Web\MVVM;

use SPT\Web\Theme;

class View
{
    protected Theme $theme;
    protected array $vm;

    public function __construct($layoutPath, $themePath, $theme)
    {
        if(empty($themePath) || empty($theme))
        {
            $themePath = $layoutPath;
        }
        else
        {
            $themePath .= '/'. $theme;
        }

        if( '/' != substr($themePath, -1))
        {
            $themePath .= '/';
        }

        if($themePath == $layoutPath)
        {
            $overrideLayouts = [
                $layoutPath. '__.php',
                $layoutPath. '__/index.php'
            ];
        }
        else
        {   
            $overrideLayouts = [
                $themePath. '__.php',
                $themePath. '__/index.php',
                $layoutPath. '__.php',
                $layoutPath. '__/index.php'
            ];
        }
        
        $this->theme = new Theme($themePath, $overrideLayouts);
        $this->vm = [];
    }

    public function renderPage(string $page, string $layout, array $data = [])
    {
        $this->theme->setBody(
            $this->renderLayout($layout, $data)
        );
        return $this->theme->render( $page, $data );
    }

    public function renderLayout(string $layout, array $data = [])
    {
        if( 0 !== strpos($layout, 'layouts.') )
        {
            $layout = 'layouts.'. $layout;
        }
        $this->deployVM($layout, $data);
        return $this->theme->renderLayout( $layout, $data );
    }

    public function registerVM($layout, $vm)
    {
        if(!isset($this->vm[$layout]))
        {
            $this->vm[$layout] = [];
        }

        $try = explode('|', $layout);
        if( sizeof($try) > 1)
        {
            $layout = array_shift($try);
            $this->vm[$layout][] = [$vm, $try];
        }
        else
        {
            $try = explode('.', $layout);
            $try = end( $try );
            $this->vm[$layout][] = [$vm, [$try]];
        }
    }

    public function deployVM($layout, &$data)
    {
        if(isset($this->vm[$layout]))
        {
            foreach($this->vm[$layout] as $array)
            {
                list($vm, $functions) = $array;
                 
                $ViewModel = new $vm();
                foreach($functions as $fnc)
                {   
                    if(method_exists($ViewModel, $fnc))
                    {
                        $data = array_merge($data, $ViewModel->$fnc());
                    }
                }
            }
        }
    }
}