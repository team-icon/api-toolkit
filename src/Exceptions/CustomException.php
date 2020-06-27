<?php
    namespace TeamIcon\TeamIconApiToolkit\Exceptions;
    use \TeamIcon\Logger;

    require_once(__DIR__ . "/../Logger.php");

    class CustomException extends \Exception {
        public function __construct(string $message, int $httpCode = 400) {
            $type = get_called_class();
            $msg = "An exception of type $type occoured with these error message: $message";
            parent::__construct($msg, $httpCode, null);
            Logger::WriteError($msg);
        }
    }
?>