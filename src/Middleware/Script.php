<?php
/**
 * SPT software - Middleware script
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Middleware script Abstract
 * 
 */

namespace SPT\Middleware;

abstract class Script
{
    /**
     * @var Middleware
     */
    protected $next;

    /**
     * This method can be used to build a chain of middleware objects.
     */
    public function linkWith(Middleware $next): Middleware
    {
        $this->next = $next;

        return $next;
    }

    /**
     * This method demo how the function for a chain
     */
    public function test($input): bool
    {
        if (!$this->next) {
            return true;
        }

        return $this->next->test($input);
    }
}