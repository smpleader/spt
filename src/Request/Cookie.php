<?php
/**
 * SPT software - Request Cookie
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Request by Cookie
 * 
 */

namespace SPT\Request;

use SPT\Support\Env;

class Cookie extends Base
{    
  public function __construct(?array $source = null)
  {
    $this->data = & $_COOKIE;
  }

  public function set($name, $value)
  {
    if(!Env::isCli())
    {
      // TODO should have better way!
      setcookie($name, $value, time() + 86400, '/');
    }

    parent::set($name, $value);
  }
}
