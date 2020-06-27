<?php
    namespace teamicon\apikit\Exceptions;

    require_once(__DIR__ . "/CustomException.php");

    class InvalidTypeException extends CustomException {
        protected string $fieldName;
        protected string $fieldType;
        protected string $typeChecked;

        public function __construct(string $fieldName, string $fieldType, string $typeChecked, string $note = "", int $httpCode = 400) {
            $msg = "The field $fieldName of type $fieldType is different by the type $typeChecked" . ($note != "" ? ", Note: $note" : "");
            parent::__construct($msg, $httpCode);
            $this->fieldName = $fieldName;
            $this->fieldType = $fieldType;
            $this->typeChecked = $typeChecked;
        }

        public function GetFieldName() : string { return $this->fieldName; }
        public function GetFieldType() : string { return $this->fieldType; }
        public function GetFieldChecked() : string { return $this->typeChecked; }
    }
?>