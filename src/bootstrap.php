<?php
/**
 * SPT software - Bootstrap
 * This write for no PSR usage, or no Composer
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: We can implemet auto load here, but just make the minium of jobs
 * 
 */

defined( 'APP_PATH' ) or die('You must define Application path first');

define( 'SPT_PATH', __DIR__ . '/');

require_once 'BaseObj.php';
require_once 'StaticObj.php';
require_once 'FncArray.php';
require_once 'FncObject.php';
require_once 'Util.php';
require_once 'Config.php';
require_once 'Log.php';
require_once 'Lang.php';
require_once 'Theme.php';
require_once 'Asset.php';
require_once 'PdoWrapper.php';
require_once 'Query.php';
require_once 'Response.php';