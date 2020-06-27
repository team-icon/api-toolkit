<?php
    namespace TeamIcon\TeamIconApiToolkit;

    final class ApiResult {
        public const OK = "OK";
        public const KO = "KO";

        private string $Response;
        private string $ErrorMessage;
        private int $HttpCode;
        private array $Payload;

        private function __construct(bool $isOk, array $payload = [], string $errMsg = "", int $code = 200) {
            if($isOk) {
                $this->Response = self::OK;
                $this->ErrorMessage = "";
                $this->HttpCode = 200;
                $this->Payload = $payload;
            } else {
                $this->Response = self::KO;
                $this->ErrorMessage = $errMsg;
                $this->HttpCode = $code;
                $this->Payload = [];
            }
        }

        private function GetArray() : array {
            $arr = [];
            $arr["Response"] = $this->Response;
            $arr["ErrorMessage"] = $this->ErrorMessage;
            $arr["HttpCode"] = $this->HttpCode;
            $arr["Payload"] = $this->Payload;
            return $arr;
        }

        public static function Ok(array $payload) : array {
            $ar = new ApiResult(true, $payload);
            return $ar->GetArray();
        }

        public static function Ko(string $errMsg, int $errCode = 400) : array {
            $ar = new ApiResult(false, [], $errMsg, $errCode);
            return $ar->GetArray();
        }
    }
?>