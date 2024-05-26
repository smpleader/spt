<?php
/**
 * SPT software - Request Cookie
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Request by Post
 *          known issue with multipart form data vs php://input can be solved by installing
 * 
 */

namespace SPT\Request;

use SPT\Support\ParseInputStream;

class Post extends Base
{
    public function __construct(array $source = null)
    {
        $request = Singleton::instance();
        $type = $request->header->get('Content-Type', '--', 'raw');
        switch($type)
        {
            case 'application/x-www-form-urlencoded': // query string
            default:
                break;
            case 'application/json':
                $content = file_get_contents("php://input");
                $_POST = json_decode($content, true); 
                break;
            case 'multipart/form-data':
                // 2 solutions:
                // https://pecl.php.net/package/apfd
                // https://gist.github.com/devmycloud/df28012101fbc55d8de1737762b70348 
                $input = array();
                new ParseInputStream($input);
                
                foreach ($input as $key => $param) {
                    if ($param instanceof \SPT\Support\UploadFile) {
                        $_FILES[$key] = $param;
                    } else {
                        $_POST[$key] = $param;
                    }
                }
                break;
        }

        $this->data = & $_POST;
    }
}