<?php
    namespace teamicon\apikit;

    use Throwable;

    final class Utility {
        public static function Curl(string $url, string $method, bool $forceHeaders = false, array $headers = [], array $params = []) : array {
            $ch = null;
            if(count($headers) > 0) {
                $ct_found = false;
                foreach($headers as $h) if(strtolower(substr(trim($h),0, 13)) == "content type:") { $ct_found = true; break; }
                if(!$ct_found && $forceHeaders) $headers[] = "Content Type: application/json";
            } else if($forceHeaders) $headers[] = "Content Type: application/json";
            $ch = curl_init($url);
            if($method != "GET" && $params != null && count($params) > 0) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            }
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            $res = curl_exec($ch);
            $arr = json_decode($res, true);
            if($arr == null) throw new ApiKitException("Curl error " . curl_error($ch));
            curl_close($ch);
            return $arr != null ? $arr : [];
        }

        public static function GenerateRandomToken(int $numOfChar) : string {
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
            } catch(Throwable $ex) {
                new ApiKitException("Generic error (code " . $ex->getCode() . "): " . $ex->getMessage(), $ex->getCode());
                return "";
            }
        }

        public static function GetClientIp() : string {
            if(isset($_SERVER['HTTP_CLIENT_IP'])) $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            else if(isset($_SERVER['HTTP_X_FORWARDED'])) $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            else if(isset($_SERVER['HTTP_FORWARDED'])) $ipaddress = $_SERVER['HTTP_FORWARDED'];
            else if(isset($_SERVER['REMOTE_ADDR'])) $ipaddress = $_SERVER['REMOTE_ADDR'];
            else $ipaddress = 'UNKNOWN';
            return $ipaddress;
        }

        public  static function GetClientLanguage() : string {
            return isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : "";
        }
    }
?>