<?php
function errorHandler($errno, $errstr, $errfile, $errline) {
    error_log("Error: [$errno] $errstr in $errfile on line $errline");
    
    if (!(error_reporting() & $errno)) {
        return;
    }
    
    http_response_code(500);
    die("An error occurred. Please try again later.");
}

function exceptionHandler($exception) {
    error_log("Exception: " . $exception->getMessage());
    http_response_code(500);
    die("An unexpected error occurred. Please try again later.");
}

set_error_handler('errorHandler');
set_exception_handler('exceptionHandler');