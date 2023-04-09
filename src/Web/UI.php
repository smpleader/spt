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

class UI
{
    protected array $menus = [];

    public function getMenu($menuIds = null)
    {
        return $menuIds === null ? $this->menus :
            ( $menuIds === '__FIRST__' && count($this->menus) ? array_shift($this->menus) : 
                ( isset($this->menus[$menuIds]) ? $this->menus[$menuIds] : false ) );
    }

    public function generate($menuId = '__FIRST__')
    {
        $menu = $this->getMenu($menuId);

        if(!is_a($menu, '\SPT\View\Gui\Menu'))
        {
            throw new \Exception('Invalid menu Ids'); 
        }

        if(file_exists($menu->getLayout()))
        {
            
        }

        $_output = '';



        if($this->link)
        {
            $_output .= '<a href="'. $this->link. '" >';
        }

        if( $this->alias )
        {
            if( is_string($this->alias))
            {
                $key = $this->alias;
                $_output .= $row->{$key}; 
            }
            /*elseif( is_array($this->alias))
            {
                list($name, $fnc) = $this->alias;
                $model = $this->getModel( $name ); // TODO consider use function from field
                $_output .= $model->$fnc( $row, $this->id );
            }*/
        }
        else
        {
            $key = $this->id;
            $_output .= $row->{$key};  
        }

        if($this->link && $link)
        {
            $_output .= '</a>';
        }

        return $_output;
    }
    
    /**
     * SUPPORT form + field
     */
    public function form($formName = null)
    {
        $sth = $this->form;
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
                $this->field = $form->getField();
                $layout = $this->field->layout ? $this->field->layout : 'fields.'. $this->field->type;
            }
        }
        else
        {
            $this->field = $form->getField($name);
            $layout = $this->field->layout ? $this->field->layout : 'fields.'. $this->field->type;
        }

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