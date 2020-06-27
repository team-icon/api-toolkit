<?php
    namespace teamicon\apikit;

    require_once(__DIR__ . "/RouteParameters.php");

    abstract class Entity {
        abstract static function GetItem(array $pk) : ?Entity;
        abstract static function GetAll() : array;
        abstract function ToArray() : array;
        abstract static public function CreateFromArray(array $arr) : Entity;
        abstract static function Exists(array $pk) : bool;
        abstract static function Route(RouteParameters $rp) : array;
    }
?>