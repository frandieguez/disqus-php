<?php
namespace Disqus\Exception;

class DisqusAPIError extends Exception
{
    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }
}
