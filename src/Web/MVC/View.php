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
use SPT\Web\ViewLayout;

class View
{
    use \SPT\Web\ViewTrait;

    public function renderPage(string $page, string $layout, array $data = [])
    {
        if( 0 !== strpos($layout, 'layouts.') )
        {
            $layout = 'layouts.'. $layout;
        }

        if($this->mainLayout)
        {
            throw new \Exception('Generate page twice is not supported ');
        }
        else
        {
            $this->mainLayout = $layout;
        }

        $file = $this->theme->getThemePath(). '/'. $page. '.php';
        if( !file_exists($file) )
        {
            throw new \Exception('Invalid theme page '. $page);
        }

        if(is_array($data) || is_object($data))
        {
            foreach($data as $key => $value)
            {
                $this->setVar($key, $value);
            }
        }

        ob_start();
        include $file;
        $content = ob_get_clean();

        return $content; 
    }
    
    public function renderLayout(string $layoutPath, array $data = [])
    {
        if( 0 !== strpos($layoutPath, 'layouts.') )
        {
            $layoutPath = 'layouts.'. $layoutPath;
        }
        $file = $this->getPath($layoutPath);
        if( false === $file )
        {
            throw new \Exception('Invalid layout '. $layoutPath);
        }

        $layout = new ViewLayout($file, $this);
        foreach($data as $key => $value)
        {
            $layout->set($key, $value);
        }
        
        return $layout->_render();
    }

    public function renderWidget(string $widgetPath, array $data = [])
    {
        if( 0 !== strpos($widgetPath, 'widgets.') )
        {
            $widgetPath = 'widgets.'. $widgetPath;
        }
        $file = $this->getPath($widgetPath);
        if( false === $file )
        {
            throw new \Exception('Invalid widget '. $widgetPath);
        }

        $layout = new ViewLayout($file, $this);
        foreach($data as $key => $value)
        {
            $layout->set($key, $value);
        }
        
        return $layout->_render();
    }
}