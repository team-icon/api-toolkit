<?php
    namespace TeamIcon\TeamIconApiToolkit\Traits;
    use \TeamIcon\Logger;

    require_once(__DIR__ . "/../Logger.php");

    trait SessionUtilities {
        protected static function SaveSessionToken(string $token) : bool {
            if($token == "") { Logger::WriteError("Tentato salvataggio token sessione con token vuoto"); return false; }
            $_SESSION["SessionToken"] = $token;
            return true;
        }

        protected static function GetSessionToken() : string { return $_SESSION["SessionToken"]; }
    }
?>