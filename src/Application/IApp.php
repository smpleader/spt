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
    function getContainer();
    function getRequest();
    function getRouter();
    function execute(string $templatePath);
}