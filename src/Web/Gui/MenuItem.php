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

class MenuItem
{
    public function __construct( $id, string $link, array $params)
    {
        $this->id = $id;
        $this->link = $link;
        $this->title = isset( $params['title']) ? $params['title'] : str_replace('_', ' ', ucfirst($id)); 
        $this->label = isset( $params['label']) ? $params['label'] : str_replace('_', ' ', ucfirst($id)); 
        $this->class = isset( $params['class']) ? $params['class'] : '';
        $this->level = isset( $params['level']) ? $params['level'] : '';
        $this->menu = isset( $params['menu']) ? $params['menu'] : ''; 
    } 
}