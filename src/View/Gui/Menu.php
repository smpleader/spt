<?php
/**
 * SPT software - Gui Field
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Easily display data object property
 * 
 */

namespace SPT\View\Gui;

class Menu
{
    protected array $menuitems = [];
    protected $layout;

    public function __construct(int|string $id, string $layout, array $menuitems = [])
    {
        $this->id = $id;
        $this->layout = $layout;
        $this->import($menuitems);
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function getItems()
    {
        return $this->menuitems;
    }

    public function import(array $menuitems)
    {
        if(count($menuitems))
        {
            foreach($menuitems as $item)
            {
                $this->add($item);
            }
        }
    }

    public function add(MenuItem|arrray $item)
    {
        if(is_array($item))
        {
            list($id, $link, $params) = $item;
            if(empty($link)) $link = '#';
            if(!is_array($params)) $params = ['title' => $params];
            $this->menuitems[] = new MenuItem($id, $link, $params);
        }
        elseif(is_a($item, 'MenuItem'))
        {
            $this->menuitems[] = $item;
        }
    }

    // $find could be zero, check with false to see remove result
    public function remove($id)
    {
        $find = false;
        foreach($this->menuitems as $k => $item)
        {
            if($id === $item->id)
            {
                $find = $k;
            }
        }

        if(false !== $find)
        {
            unset($this->menuitems[$k]);
        }

        return $find;
    }

}