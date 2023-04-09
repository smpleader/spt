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
}