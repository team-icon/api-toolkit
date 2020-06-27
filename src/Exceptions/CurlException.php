<?php
    namespace TeamIcon\TeamIconApiToolkit\Exceptions;

    require_once(__DIR__ . "/CustomException.php");

    class CurlException extends CustomException {
        protected string $url;
        protected string $method;
        protected array $headers;
        protected array $params;
        protected string $curlErrorMessage;
        protected string $curlErrorNumber;

        public function __construct(string $url, string $method, string $cMessage, string $cErrNum, array $headers = [], array $params = [], string $note = "") {
            $message = "Curl has generated an error while it was calling $url in $method with these headers set with: ";
            if(count($headers) == 0) $message .= "nothing";
            else for($i = 0; $i < count($headers); $i++) {
                $message .= $headers[$i];
                if($i + 1 < count($headers)) $message .= ", ";
            }
            $message .= ". The parameters setted: ";
            if(count($params) == 0) $message .= "nothing";
            else for($i = 0; $i < count($params); $i++) {
                $message .= $params[$i];
                if($i + 1 < count($params)) $message .= ", ";
            }
            $message .= ". The curl message is $cMessage with error code $cErrNum.";
            if($note != "") $message .= " $note";
            parent::__construct($message);
            $this->url = $url;
            $this->method = $method;
            $this->headers = $headers;
            $this->params = $params;
            $this->curlErrorMessage = $cMessage;
            $this->curlErrorNumber = $cErrNum;
        }

        public function GetUrl() : string { return $this->url; }
        public function GetMethod() : string { return $this->method; }
        public function GetHeaders() : array { return $this->headers; }
        public function GetParameters() : array { return $this->params; }
        public function GetCurlErrorMessage() : string { return $this->curlErrorMessage; }
        public function GetCurlErrorNumber() : string { return $this->curlErrorNumber; }
    }
?>