<?php
    namespace teamicon\apikit\Traits;
    use DateTime;
    use DateTimeZone;

    trait DebugInfo {
        protected static function DebugInfo() : array {
            $debug = debug_backtrace();
            $funcName = key_exists("function", $debug) ? $debug["function"] : "n.d.";
            $className = key_exists("class", $debug) ? $debug["class"] : "n.d.";
            $dtNow = new Datetime("now", new DateTimeZone("Europe/Rome"));
            $dtString = $dtNow->format('Ymd');
            return [
                "function-name" => $funcName,
                "class-name" => $className,
                "dt-now" => $dtString
            ];
        }
    }
?>