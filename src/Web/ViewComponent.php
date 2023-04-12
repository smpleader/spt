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

class ViewComponent
{
    protected $_layout;
    public function support(ViewLayout $layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    public function translate(string $text)
    {
        // TODO: load language from app->loadPlugins('language', 'AddTranslation')
        return $text;
    }

    protected $menus;

    public function getMenu($menuIds = null)
    {
        return $menuIds === null ? $this->menus :
            ( $menuIds === '__FIRST__' && count($this->menus) ? array_shift($this->menus) : 
                ( isset($this->menus[$menuIds]) ? $this->menus[$menuIds] : false ) );
    }

    public function menu(string $menuId = '')
    {
        if( null === $menus)
        {
            // TODO set up vie factory::app->loadPlugins ..
            // current: setup vie ViewModel
        }

        return $this->_layout->render( 'vcoms.menu'.$layout);
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

        return $this->_layout->render( $layout, ['field'=>$field], 'vcoms.');

        if($layout && $file_layout = $this->_view->getPath($layout) )
        {
            ob_start();
            include $file_layout;
            $content = ob_get_clean();
            return $content;
        }

        return '<!-- Invalid field '. $name. ' in form '. $formName .' -->';
    }
}