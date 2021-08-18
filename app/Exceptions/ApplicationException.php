<?php
/**
 * @author Karl Pandacan
 * 2021-04-21
 * Application Exception
 */

namespace App\Exceptions;

use Exception;

class ApplicationException extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
    }


    public function render($request)
    {
    }
}