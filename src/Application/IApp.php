<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: An application context
 * 
 */

namespace SPT\Application;

interface IApp
{
    function plgLoad(string $event, string $execute, $closure = null);
    function execute(string $templatePath);
}