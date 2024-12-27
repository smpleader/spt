<?php
/**
 * SPT software - Base Object
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A basic object support dynamic properties
 *              - Since 0.8.38 Converted into Traits ObjectHasInternalData so this engine can reuse in other class not BaseObj
 * 
 */

namespace SPT;

use SPT\Traits\ObjectHasInternalData;

class BaseObj 
{
    use ObjectHasInternalData;
}
