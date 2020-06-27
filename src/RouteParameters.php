<?php
    namespace TeamIcon\TeamIconApiToolkit;

    final class RouteParameters {
        protected string $sc;
        protected string $lng;
        protected string $method;
        protected string $entity;
        protected string $action;
        protected array $keys;

        public function __construct(string $sc, string $lng, string $method, string $entity, string $action = "", array $keys = []) {
            $this->sc = $sc;
            $this->lng = $lng;
            $this->method = $method;
            $this->entity = $entity;
            $this->action = $action;
            $this->keys = $keys;
        }

        public function GetSC() : string { return $this->sc; }
        public function GetLanguage() : string { return $this->lng; }
        public function GetMethod() : string { return $this->method; }
        public function GetEntity() : string { return $this->entity; }
        public function GetAction() : string { return $this->action; }
        public function GetKeys() : array { return $this->keys; }
    }
?>