<?php

namespace App\Helpers;

use Exception;

class Error extends Exception
{
    public $body;

    public function __construct($body, $status, ?Exception $previous = null)
    {
        // Set the body either directly as a string or as an array
        if (is_array($body)) {
            // If the body is already an array, assume it contains more complex data
            $this->body = $body;
            $message = $body['message'] ?? json_encode($body);
        } else {
            // Otherwise, it's just a string message
            $this->body = $body;
            $message = $body;
        }

        // Set the parent exception message to the derived message string
        parent::__construct($message, $status, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getStatus()
    {
        return $this->code;
    }

    public function getBody()
    {
        return $this->body;
    }
}
