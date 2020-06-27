<?php
    namespace teamicon\apikit\Traits;
    use \teamicon\apikit\Exceptions\CustomException;

    require_once(__DIR__ . "/../Exceptions/CustomException.php");

    trait Curl {
        protected static function Curl(string $url, string $method, bool $forceHeaders = false, array $headers = [], array $params = []) : array {
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
            if($arr == null) throw new CustomException("Curl error " . curl_error($ch));
            curl_close($ch);
            return $arr != null ? $arr : [];
        }
    }
?>