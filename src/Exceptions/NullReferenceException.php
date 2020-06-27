<?php
    namespace TeamIcon\TeamIconApiToolkit\Exceptions;

    require_once(__DIR__ . "/CustomException.php");

    class NullReferenceException extends CustomException {
        protected string $fieldName;
        protected string $fieldType;

        public function __construct(string $fieldName, string $fieldType, string $note = "", int $httpCode = 400) {
            $msg = "Field $fieldName of type $fieldType is null" . ($note != "" ? ", Note: $note" : "");
            parent::__construct($msg, $httpCode);
            $this->fieldName = $fieldName;
            $this->fieldType = $fieldType;
        }

        public function GetFieldName() : string { return $this->fieldName; }
        public function GetFieldType() : string { return $this->fieldType; }
    }
?>