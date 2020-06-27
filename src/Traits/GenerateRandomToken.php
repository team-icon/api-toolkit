<?php
    namespace TeamIcon\TeamIconApiToolkit\Traits;
    use \TeamIcon\Exceptions\CustomException;

    require_once(__DIR__ . "/../Exceptions/CustomException.php");

    trait GenerateRandomToken {
        protected static function GenerateRandomToken(int $numOfChar) : string {
            if($numOfChar < 1) return "";
            try {
                $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
                $pass = "";
                for ($i = 0; $i < $numOfChar; $i++) {
                    $alphabet = str_shuffle($alphabet);
                    $n = rand(0, strlen($alphabet) - 1);
                    $pass .= $alphabet[$n];
                }
                return $pass;
            } catch(\Throwable $ex) {
                new CustomException("Generic error (code " . $ex->getCode() . "): " . $ex->getMessage(), $ex->getCode());
                return "";
            }
        }
    }
?>