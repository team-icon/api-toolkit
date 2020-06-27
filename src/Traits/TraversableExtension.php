<?php
    namespace TeamIcon\TeamIconApiToolkit\Traits;

    trait TraversableExtension {
        protected static function iterable_to_array(iterable $it): array {
            if (is_array($it)) return $it;
            $ret = [];
            array_push($ret, ...$it);
            return $ret;
        }

        protected static function iterable_to_traversable(iterable $it): \Traversable {
            yield from $it;
        }
    }
?>