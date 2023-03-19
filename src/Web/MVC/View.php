<?php
/**
 * SPT software - View
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core view
 * 
 */

namespace SPT\Web\MVC;

use SPT\Web\Theme;

class View
{
    protected Theme $theme;
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
        return $this->theme->renderLayout( $layout, $data );
    }
}