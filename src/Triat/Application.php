<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Application Triat
 * 
 */

namespace SPT\Triat;

trait Application
{
    public function getName(string $extra='')
    {
        return 'SPT\\'. $extra;
    }

    public function turnDebug($turnOn = false)
    {
        if( $turnOn )
        {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
    }

    public function redirect($url = null)
    {
        $redirect = null === $url ? $this->get('redirect', '/') : $url;
        $redirect_status = $this->get('redirectStatus', '302');

        Response::redirect($redirect, $redirect_status);
    }

    public function response($content, $code='200')
    {
        Response::_($content, $code);
    }

    public function getController(string $name)
    {
        $controllerName = $this->getName('controllers\\'.$name);
        if(!class_exists($controllerName))
        {
            throw new \Exception('Controller '. $name. ' not found', 500);
        }
        
        return new $controllerName($this);
    }

    public function getToken(string $context = '_app_')
    {
        if('_secrect_' === $context)
        {
            return empty($this->config->exists('secrect')) ? strtotime('now') : $this->config->secrect;
        }

        if(!isset($this->secrects[$context]))
        {
            $browser = $this->request->server->get('HTTP_USER_AGENT', '');
            $secrect = $this->getToken('_secrect_');

            $cookie = $this->request->cookie->get($secrect, '');
            if (!$cookie)
            {
                $this->request->cookie->set($secrect, $cookie);
            }

            $this->secrects[$context] = Token::md5(
                Token::md5($context, 4).
                Env::getClientIp().
                $browser. $cookie
            );
        } 

        return $this->secrects[$context];
    }
}