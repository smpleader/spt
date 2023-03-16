<?php
/**
 * SPT software - Asset
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: An application context
 * 
 */

namespace SPT\Application;

interface IApp
{
    function loadPlugins(string $event, string $execute, $closure = null);
    function loadConfig(string $configPath);
    function executeCommandLine(string $templatePath);
    function runWebApp(string $templatePath);
}