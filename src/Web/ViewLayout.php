<?php
/**
 * SPT software - Layout
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core layout
 * 
 */

namespace SPT\Web;

use SPT\BaseObj;

class ViewLayout extends BaseObj
{ 
    /**
    * Internal variable cache file path
    * @var string $_path
    */
    protected $_path = '';

    /**
    * Internal variable point to current view
    * @var View $_view
    */
    protected $_view;

    /**
     * Constructor
     * 
     * @param string   $filePath layout path
     * @param View   $view View instance
     * @param array   $data data used in the layout
     * 
     * @return void 
     */ 
    public function __construct(string $filePath, View $view, array $data = [])
    {
        $this->_path = $filePath;
        $this->_view = $view;
        
        if(is_array($data) && count($data))
        {
            foreach($data as $key => $value)
            {
                $this->set($key, $value);
            }
        }
    }

    /**
     * Magic get
     * 
     * @param string   $name key to output in query, could be null if not exist
     * 
     * @return mixed 
     */ 
    public function __get(string $name)
    { 
        if('theme' == $name) return $this->_view->getTheme();
        if('ui' == $name) return $this->_view->getViewComponent($this);
        if('mainContent' == $name) return $this->_view->getContent();
        // try local 
        if( isset( $this->_vars[$name] ) ) return $this->_vars[$name];
        // try global
        return $this->_view->getVar($name, NULL);
    }

    /**
     * Check exist, because using magic call cause issue if check is_null() empty()
     * 
     * @param string   $name key to output in query, could be null if not exist
     * 
     * @return bool 
     */ 
    public function exists(string $name)
    { 
        return isset( $this->_vars[$name] ) || null !== $this->_view->getVar($name, NULL);
    }

    /**
     * Render a layout, alias to renderLayout() of View
     * 
     * @param string   $layout layout path or layout name
     * @param array   $data data attached
     * @param string   $type layout type
     * 
     * @return string 
     */ 
    public function render(string $layout, array $data=[], string $type='layout')
    {
        return $this->_view->renderLayout($layout, $data, $type);
    }

    /**
     * Render a layout, alias to render() with type "widget"
     * 
     * @param string   $layout layout path or layout name
     * @param array   $data data attached
     * 
     * @return string 
     */ 
    public function renderWidget(string $layout, array $data=[])
    {
        return $this->render($layout, $data, 'widget');
    }

    /**
     * After calling renderLayout from View instance, this function will be called to keep variables attached into ViewLayout
     * 
     * @return string 
     */ 
    public function _render()
    {
        ob_start();
        include $this->_path;
        $content = ob_get_clean();
        return $content;
    }

    /**
     * Alias to translate function or print format with sprintf 
     * 
     * @param string   $vars unpredicted parameters with func_get_args
     * 
     * @return string 
     */ 
    public function txt()
    {
        $arg_list = func_get_args();
        $label = array_shift($arg_list);
        if($label)
        {
            if(count($arg_list))
            {
                return call_user_func_array('sprintf', array_unshift($arg_list, $this->ui->translate($label)));
            }
            else
            {
                return $this->ui->translate($label);
            }
        }
        
        return '';
    }

    /**
     * Alias to createUrl from ViewComponent instance
     * TODO: when ViewCompnent support SEF link generate from an object, need to upgrade this function, too
     * 
     * @param string   $alias slug to add into URL path
     * 
     * @return string 
     */ 
    public function url($alias='')
    {
        return $this->ui->createUrl($alias);
    }
}