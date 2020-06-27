<?php
    namespace teamicon\apikit\Exceptions;

    require_once(__DIR__ . "/CustomException.php");

    class NotImplementedException extends CustomException {
        public function __construct(string $msg = "", int $httpCode = 400) {
            parent::__construct("Function not implemented but it was called" . ($msg != "" ? ", $msg" : ""), $httpCode);
        }
    }
?>