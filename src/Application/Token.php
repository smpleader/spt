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
    private $storage;
    private $keyLength = 9;
    private $expireSessionDuration = 30;

    public function __construct(Configuration $config, Request $request)
    {
        $now = strtotime('now');
        $this->secrect = empty($config->secrect) ? $now : $config->secrect;
        $this->request = $request;

        $cookie = $request->cookie->get($this->secrect, '_do_not_set_');
        if ('_do_not_set_' == $cookie)
        {
            $cookie = $this->refresh();
        }
        
        $cookieTime = (int)$request->cookie->get($this->secrect. 'time', 0);
        $expireDuration = empty($config->expireSessionDuration) ? $this->expireSessionDuration : $config->expireSessionDuration;
        $expireDuration = (int)$expireDuration * 3600; // seconds
        $expireCookie = $cookieTime + $expireDuration;
        $now = (int) $now;

        if( $expireCookie < $now)
        {
            $cookie = $this->refresh();
        }
        else
        {
            $expireCookie = $now + $expireDuration;
            $request->cookie->set($this->secrect. 'time', $expireCookie);
        }

        $this->storage = ['_app_'=>$this->generate('_app_')];
    }
    
    private function refresh()
    {
        $cookie = SupportToken::md5( rand(1000001, strtotime('now')), $this->keyLength);
        $this->request->cookie->set($this->secrect, $cookie);
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