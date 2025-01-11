<?php
/**
 * SPT software - Layout Function
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic class supply functions for layout
 * 
 */
namespace SPT\Support;

use SPT\Log;
use SPT\Web\IViewFunction;
use SPT\Web\Layout\Pure;
use SPT\Support\App;
use SPT\Support\View;

class LayoutFunctions
{
    public static function registerFunctions(): array
    {
        $func = new LayoutFunctions;
        return [
            'url' =>  function ($slug) use ($func)
                {
                    return $func->getUrl($slug);
                },
            'txt' =>  function () use ($func)
                {
                            
                    $arg_list = func_get_args();
                    $label = array_shift($arg_list);
                    if($label)
                    {
                        if(count($arg_list))
                        {
                            return call_user_func_array('sprintf', array_unshift($arg_list, $func->translate($label)));
                        }
                        else
                        {
                            return $func->translate($label);
                        }
                    }
                    
                    return '';
                },
            'form' => function($name = null)
                {
                    if(is_array($this->form))
                    {
                        if(!count($this->form)) return false;
                        if(isset($this->form[$name])) return $this->form[$name];
            
                        reset($this->form);
                        return current($this->form);
                    }
            
                    return false;
                },
            'field' => function ($name = null, $formName = null)
                {
                    echo $this->_field($name, $formName);
                },
            '_field' => function ($name = null, $formName = null)
                {
                    $form = $this->form($formName);
                    if(!$form) return '';
                    
                    $field = false;
                    if(null === $name)
                    {
                        if($form->hasField())
                        {
                            $field = $form->getField();
                        }
            
                        if(false === $field)
                        {
                            return '!! <!-- No field found -->';
                        } 
                    }
                    else
                    {
                        $field = $form->getField($name);
            
                        if(false === $field)
                        {
                            return '!! <!-- Field "'. $name. '" not found -->';
                        } 
                    }

                    $layout = empty($field->layout) ? ':viewcom:fields.'.$field->type : $field->layout;
                    if(false === strpos($layout, ':')) $layout = ':viewcom:fields.'. $layout;
            
                    return View::render( $layout, ['field'=>$field]);
                }
        ]; 
    }

    public function getUrl(string $alias)
    {
        $container = App::getInstance()->getContainer();
        return $container->get('router')->url($alias);
    }

    public function translate(string $txt)
    {
        // TODO: support multi language
        return $txt;
    }
}
