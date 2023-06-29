<?php
/**
 * SPT software - Gui libraries support multi site elements
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Easily display data object property
 * 
 */

namespace SPT\Web;

use SPT\Application\IRouter;

class ViewComponent
{
    protected $router;
    public function __construct(IRouter $router)
    {
        $this->router = $router;
    }

    protected $_layout;
    public function support(ViewLayout $layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    public function createUrl($alias = '')
    {
        // TODO: support generate url from object
        return $this->router->url($alias);
    }

    public function translate(string $text)
    {
        // TODO: load language from app->plgLoad('language', 'AddTranslation')
        return $text;
    }

    protected $title;
    public function title(string $text = '////')
    {
        if('////' === $text)
        {
            echo $this->_layout->exists('title') ? $this->_layout->title : $this->title;
        }
        else
        {
            $this->title = $text;
        }
    }

    protected $menus;
    public function menu(string $menuId = '')
    {
        if( null === $this->menus)
        {
            // TODO set up vie factory::app->plgLoad ..
            // current: setup vie ViewModel
        }

        return $this->_layout->render( 'vcoms.menu'.$menuId, [], 'vcom');
    }
    
    public function form($formName = null)
    {
        $sth = $this->_layout->form;
        if(is_array($sth))
        {
            if(!count($sth)) return false;
            if(isset($sth[$formName])) return $sth[$formName];

            reset($sth);
            return current($sth);
        }

        return $sth;
    }

    public function field($name = null, $formName = null)
    {
        echo $this->_field($name, $formName);
    }

    public function _field($name = null, $formName = null)
    {
        $form = $this->form($formName);
        if(!$form) return;
        
        $layout = false;
        if(null === $name)
        {
            if($form->hasField())
            {
                $field = $form->getField();
                $layout = $field->layout ? $field->layout : 'fields.'. $field->type;
            }
        }
        else
        {
            $field = $form->getField($name);
            $layout = $field->layout ? $field->layout : 'fields.'. $field->type;
        }

        return $this->_layout->render( $layout, ['field'=>$field], 'vcom');
 
    }
}