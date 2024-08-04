<?php
/**
 * SPT software - Application token
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A class to support to identify end user with token engine
 * @version: 0.8
 * 
 */

namespace SPT\Application;

use SPT\Application\IApp;
use SPT\Application\Configuration;
use SPT\Request\Base as Request;
use SPT\Support\Token as SupportToken;
use SPT\Support\Env;

class Token
{
    private $secrect;
    private $request;
    private $storage;
    private $keyLength = 9;
    private $expireSessionDuration = 30;

    public function __construct(Configuration $config, Request $request)
    {
        $now = strtotime('now');
        $this->secrect = $config->exists('secrect') ? $config->secrect : '__NO_PRODUCT_MODE__';
        $this->request = $request;
        $expireDuration = $config->exists('expireSessionDuration') ? $config->expireSessionDuration : $this->expireSessionDuration;
        $expireDuration = (int)$expireDuration * 60; // seconds
        
        $cookie = $request->cookie->get($this->secrect, '_do_not_set_');
        if ('_do_not_set_' == $cookie)
        {
            $cookie = $this->refresh($expireDuration);
        }
        
        $cookieTime = (int)$request->cookie->get($this->secrect. 'time', 0);
        if( 0 !== $cookieTime || '0' !== $cookieTime )
        {
            $expireCookie = $cookieTime + $expireDuration;
            $now = (int) $now;
    
            if( $expireCookie < $now)
            {
                $cookie = $this->refresh($expireDuration);
            }
            else
            {
                $expireCookie = $now + $expireDuration;
                $request->cookie->set($this->secrect. 'time', $expireCookie);
            }
        }

        $this->storage = ['_app_'=>$this->generate('_app_')];
    }
    
    private function refresh($duration = 0)
    {
        $cookie = SupportToken::md5( rand(1000001, strtotime('now')), $this->keyLength);
        $this->request->cookie->set($this->secrect, $cookie);
        $this->request->cookie->set($this->secrect. 'time', 0 === $duration ? 0 : (strtotime('now') + $duration) );
        return $cookie;
    }

    private function generate(string $context)
    {
        $browser = $this->request->server->get('HTTP_USER_AGENT', '');

        $cookie = $this->request->cookie->get($this->secrect, '--');

        return SupportToken::md5(
            SupportToken::md5($context, 4). Env::getClientIp(). $browser. $cookie
        );
    }

    public function value(string $context = '_app_')
    {
        if(!isset($this->storage[$context]))
        {
            $this->storage[$context] = $this->generate($context);
        }
        
        return $this->storage[$context];
    }

    public function validate(string $token, string $context = '_app_')
    {
        $res = $this->value($context);
        return $res === $token;
    }
}