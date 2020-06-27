<?php
    namespace teamicon\apikit;

    require_once(__DIR__ . "/Entity.php");

    abstract class DynamicEntity extends Entity {
        abstract public function Update() : bool;
        abstract public function Delete() : bool;
        abstract protected function Insert() : bool;
        abstract protected function Edit() : bool;
        abstract public static function Remove(array $pk) : bool;
    }
?>