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
    /**
    * Internal variable to point to router, this help the SEF link
    * @var IRouter $router
    */
    protected $router;

    /**
    * Internal variable to cache current layout instance
    * @var ViewLayout $_layout
    */
    protected $_layout;

    /**
    * Internal variable to show menu layout
    * @var mixed $menus
    */
    protected $menus = null;
    
    /**
     * Constructor
     * 
     * @return void 
     */ 
    public function __construct(IRouter $router)
    {
        $this->router = $router;
    }

    /**
     * Point out which layout we need to support
     *
     * @param ViewLayout   $layout layout instance to attach
     * 
     * @return ViewComponent 
     */
    public function support(ViewLayout $layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Create URL based current alias
     * TODO: support generate url from an object
     *
     * @param string   $alias url path
     * 
     * @return string 
     */
    public function createUrl(string $alias = '')
    {
        return $this->router->url($alias);
    }

    /**
     * Support translate a text
     * TODO: load language from app->plgLoad('language', 'AddTranslation')
     *
     * @param string   $text text to be translated
     * 
     * @return string 
     */
    public function translate(string $text)
    {
        return $text;
    }

    /**
     * Show viewcomponent menu by render menu layout
     * TODO: set up vie factory::app->plgLoad when menu not set
     * Other way: setup vie ViewModel
     *
     * @param string   $menuLayout menu layout name
     * @param array   $data pass menuitems here
     * 
     * @return string 
     */
    public function menu(string $menuLayout = '', array $data = [])
    {
        if( null === $this->menus)
        {
            //.. setup menus by plgLoad here and add into $data
        }

        return $this->_layout->render( 'vcoms.menu'.$menuLayout, $data, 'vcom');
    }

    /**
     * Get form from a layout variable by name
     *
     * @param string   $name get form by its name
     * 
     * @return bool | GUI/Form | null | array
     */
    public function form($name = null)
    {
        $sth = $this->_layout->form;
        if(is_array($sth))
        {
            if(!count($sth)) return false;
            if(isset($sth[$name])) return $sth[$name];

            reset($sth);
            return current($sth);
        }

        return $sth;
    }

    /**
     * Echo field by name 
     *
     * @param string   $name field name
     * @param string   $formName form name
     * 
     * @return void
     */
    public function field($name = null, $formName = null)
    {
        echo $this->_field($name, $formName);
    }

    /**
     * Return string body of a field by name 
     *
     * @param string   $name field name
     * @param string   $formName form name
     * 
     * @return string
     */
    public function _field($name = null, $formName = null)
    {
        $form = $this->form($formName);
        if(!$form) return '';
        
        $field = false;
        if(null === $name)
        {
            if($form->hasField())
            {
                $field = $form->getField();
                if(!isset($field->layout))
                {
                    return '!! <!-- Field '. $field->type. ' ('. $field->id. ') needs a layout -->';
                }
            }

            if(false === $field)
            {
                return '<!-- None field found -->';
            } 
        }
        else
        {
            $field = $form->getField($name);

            if(false === $field)
            {
                return '<!-- Field "'. $name. '" not found -->';
            } 

            if(!isset($field->layout))
            {
                return '!! <!-- Field '. $field->type. ' ('. $field->id. ') needs a layout -->';
            }
        }

        return $this->_layout->render( $field->layout, ['field'=>$field], 'vcom');
    }
}