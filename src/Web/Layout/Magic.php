<?php
/**
 * SPT software - Layout
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a view layout using magic methods
 * 
 */

namespace SPT\Web\Layout;

class Magic extends Base
{
    /**
    * Internal variable point to current view
    * @var View $_view
    */
    protected View $_view;

    /**
    * Internal variable point to viewmodel
    * @var ViewModel $_viewmodel
    */
    protected ?ViewModel $_viewmodel;

    /**
     * Constructor
     * 
     * @param string   $filePath layout path
     * @param View   $view View instance
     * @param array   $data data used in the layout
     * 
     * @return void 
     */ 
    public function __construct(string $path, View $view, ?ViewModel $viewmodel)
    {
        $this->_path = $path;
        $this->_view = $view;
        $this->_viewmodel = $viewmodel;
        $this->_viewmodel->prepareData($path);
    }
/*
    public function render(string $path)
    {
        // 1 - convert path into real path
        list($plugin, $path) = explode('::', $path);
        // if using current plugin, find it
        //if(empty($plugin))
        // 2- differ layout by renderLayout vs renderWidget or component
        // 3- view store data from controller
        // 4- call viewmodel everytime render a layout
        // *** controller use viewmodel to create data for main view
        // function toHtml() --> call layout of theme, viewmodel of theme auto loaded
    }*/

    /**
     * Magic get
     * 
     * @param string   $name key to output in query, could be null if not exist
     * 
     * @return mixed 
     */ 
    public function __get(string $name)
    { 
        //if('theme' == $name) return $this->_view->getTheme();
        //if('ui' == $name) return $this->_view->getViewComponent($this);
        //if('mainContent' == $name) return $this->_view->getContent();
        // try local 
        if( $this->_viewmodel->exists($name) ) return $this->_viewmodel->get($name);
        // try global
        return $this->_view->get($name, NULL);
    }

    public function __call($method, $args)
	{
		if (!in_array($method, array_keys($this->functions))) {
			throw new BadMethodCallException();
		}

		array_unshift($args, $this->s);

		return call_user_func_array($this->functions[$method], $args);
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
}