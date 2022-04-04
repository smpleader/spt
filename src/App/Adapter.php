<?php
/**
 * SPT software - Application Adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Application Adapter
 * 
 */

namespace SPT\App;

interface Adapter
{
    public function has(string $name);
    public function getName(string $name);
    public function getToken(string $context = '');
    public function response($content, $code = '200');
    public function execute();
}
