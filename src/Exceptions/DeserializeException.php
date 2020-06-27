<?php
    namespace TeamIcon\TeamIconApiToolkit\Exceptions;

    require_once(__DIR__ . "/CustomException.php");

    class DeserializeException extends CustomException {
        protected string $serializedString;

        public function __construct(string $serializedString, string $note = "", int $httpCode = 400) {
            $message = "The error occurred while it was trying to deserialize this string: $serializedString in associative array";
            if($note != "") $message .= ". $note.";
            parent::__construct($message, $httpCode);
            $this->serializedString = $serializedString;
        }

        public function GetSerializedString() : string { return $this->serializedString; }
    }
?>