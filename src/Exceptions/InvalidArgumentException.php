<?php
    namespace teamicon\apikit\Exceptions;

    require_once(__DIR__ . "/CustomException.php");

    class InvalidArgumentException extends CustomException {
        protected string $field;
        protected string $value;

        public function __construct(string $field, string $value, string $note = "", int $httpCode = 400) {
            $msg = "The field $field had the value $value" . ($note != "" ? ", Note: $note" : "");
            parent::__construct($msg, $httpCode);
            $this->field = $field;
            $this->value = $value;
        }

        public function GetField() : string { return $this->field; }
        public function GetValue() : string { return $this->value; }
    }
?>