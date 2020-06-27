<?php
    namespace teamicon\apikit;
    use \teamicon\apikit\Traits\DebugInfo;

    require_once(__DIR__ . "/Traits/DebugInfo.php");

    final class Logger {
        private const ERROR = "code-errors";
        private const ANOMALIES = "anomalies";
        private const CALL = "calls";
        private const TRACE = "trace";

        use DebugInfo;

        private static function GetInfo(string $type) : array {
            $info = static::DebugInfo();
            $fileName = $info["dt-now"] . ".log";
            $directory = __DIR__ . "/../logs/$type/";
            $pathFile = $directory . $fileName;
            return [
                "function-name" => $info["function-name"],
                "class-name" => $info["class-name"],
                "dt-now" => $info["dt-now"],
                "file-name" => $fileName,
                "directory" => $directory,
                "path-of-file" => $pathFile
            ];
        }

        public static function WriteError(string $msg) : void {
            $info = self::GetInfo(self::ERROR);
            $txt = "[" . $info["dt-now"] . "] Error in " . $info["class-name"] . "->" . $info["function-name"] . " with this error message: $msg";
            file_put_contents($info["path-of-file"], $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
        }

        public static function WriteAnomaly(string $msg) : void {
            $info = self::GetInfo(self::ANOMALIES);
            $txt = "[" . $info["dt-now"] . "] Anomaly catched in " . $info["class-name"] . "->" . $info["function-name"] . " with this message: $msg";
            file_put_contents($info["path-of-file"], $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
        }

        public static function WriteUri(string $sc, string $method, string $uri) : void {
            $info = self::GetInfo(self::CALL);
            $txt = "[" . $info["dt-now"] . "] called $method on $uri with sc $sc";
            file_put_contents($info["path-of-file"], $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
        }

        public static function WriteTrace(string $msg, bool $debug = false) : void {
            $info = self::GetInfo(self::TRACE);
            $txt = "[" . $info["dt-now"] . "]" . ($debug ? "DEBUG :: " : "") . "$msg";
            file_put_contents($info["path-of-file"], $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
        }
    }
?>