<?php
    namespace teamicon\apikit\Exceptions;

    use \teamicon\apikit\RouteParameters;

    require_once(__DIR__ . "/CustomException.php");
    require_once(__DIR__ . "/../RouteParameters.php");

    class RouteException extends CustomException {
        protected string $url;
        protected RouteParameters $rp;

        public function __construct(string $url, RouteParameters $rp, string $note = "", int $httpCode = 400) {
            $sc = $rp->GetSC();
            $method = $rp->GetMethod();
            $entity = $rp->GetEntity();
            $action = $rp->GetAction();
            $keys = $rp->GetKeys();
            if($entity == "") $entity = "n.d.";
            if($action == "") $action = "n.d.";
            $message = "An error occurred while it was trying to route $url by $method with sc $sc, entity $entity, action $action and keys: ";
            if(count($keys) == 0) $message .= "nothing";
            else {
                for($i = 0; $i < count($keys); $i++) {
                    $message .= $keys[$i];
                    if($i + 1 < count($keys)) $message .= ", ";
                }
            }
            if($note != "") $message .= ". $note.";
            parent::__construct($message, $httpCode);
            $this->url = $url;
            $this->rp = $rp;
        }

        public function GetUrl() : string { return $this->url; }
        public function GetRouteParameters() : string { return $this->rp; }
    }
?>