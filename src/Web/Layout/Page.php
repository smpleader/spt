<?php
/**
 * SPT software - Layout Page
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Class to generate page
 * 
 */

namespace SPT\Web\Layout;

class Page extends Base
{
    /**
     * Constructor
     * 
     * @param string   $filePath layout path
     * @param array   $dataView data from View
     * @param array   $dataViewModel data from ViewModel
     * 
     * @return void 
     */ 
    public function __construct(string $path, array $dataView, array $dataViewModel)
    {
        $file = $this->theme->path. '/'. $page. '/index.php';
        if( !file_exists($file) )
        {
            $file = $this->theme->path. '/'. $page. '.php';
        }
        
        if( !file_exists($file) )
        {
            throw new \Exception('Invalid theme page '. $page);
        }

        $this->setVar('mainLayout', $layout);

        if($this->isMVVM)
        {
            ViewModelHelper::deployVM('layout', $layout, $data, []);
        }

        if(is_array($data) || is_object($data))
        {
            foreach($data as $key => $value)
            {
                $this->setVar($key, $value);
            }
        }

        $vlayout = new ViewLayout($file, $this);
        if($prepareMain)
        {
            $this->mainContent = $vlayout->render($layout);
        }

        return $vlayout->_render();
    }
}