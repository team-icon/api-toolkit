<?php
    namespace teamicon\apikit;

    use DateTime;
    use DateTimeZone;

    final class Logger {
        private const ERROR = "code-errors";
        private const ANOMALIES = "anomalies";
        private const CALL = "calls";
        private const TRACE = "trace";

        private string $root;

        public function __construct(string $logRoot) {
            if($logRoot == "") throw new ApiKitException("The logRoot parameter is empty");
            $this->root = $logRoot;
        }

        private function GetInfo(string $type) : array {
            $info = self::DebugInfo();
            $fileName = $info["filename"] . ".log";
            $directory = $this->root . "/$type/";
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

        public function WriteError(string $msg) : void {
            $info = $this->GetInfo(self::ERROR);
            $txt = "[" . $info["dt-now"] . "] Error in " . $info["class-name"] . "->" . $info["function-name"] . " with this error message: $msg";
            file_put_contents($info["path-of-file"], $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
        }

        public function WriteAnomaly(string $msg) : void {
            $info = $this->GetInfo(self::ANOMALIES);
            $txt = "[" . $info["dt-now"] . "] Anomaly catched in " . $info["class-name"] . "->" . $info["function-name"] . " with this message: $msg";
            file_put_contents($info["path-of-file"], $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
        }

        public function WriteUri(string $sc, string $method, string $uri) : void {
            $info = $this->GetInfo(self::CALL);
            $txt = "[" . $info["dt-now"] . "] called $method on $uri with sc $sc";
            file_put_contents($info["path-of-file"], $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
        }

        public function WriteTrace(string $msg, bool $debug = false) : void {
            $info = $this->GetInfo(self::TRACE);
            $txt = "[" . $info["dt-now"] . "]" . ($debug ? "DEBUG :: " : "") . "$msg";
            file_put_contents($info["path-of-file"], $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
        }

        private function DebugInfo() : array {
            $debug = debug_backtrace();
            $funcName = key_exists("function", $debug) ? $debug["function"] : "n.d.";
            $className = key_exists("class", $debug) ? $debug["class"] : "n.d.";
            $dtNow = new Datetime("now", new DateTimeZone("Europe/Rome"));
            return [
                "function-name" => $funcName,
                "class-name" => $className,
                "filename" => $dtNow->format('Ymd'),
                "dt-now" => $dtNow->format('d/m/Y H:i:s')
            ];
        }
    }
?>