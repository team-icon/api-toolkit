<?php
    namespace teamicon\apikit;
    use Throwable;

    final class RouteManager {
        public static array $Info = [];
        private RouteParameters $rp;

        private function __construct() {
            $tokens = [];
            $sc = self::$Info["sc"];
            $lng = self::$Info["lng"];
            $uri = self::$Info["uri"];
            $method = self::$Info["method"];
            if($uri == "" || $uri == "/") {
                $ip = Utility::GetClientIp();
                throw new ApiKitException("Attemp to crack the system detected from ip $ip");
            }
            if(!in_array(strtoupper($method), ["POST", "PATCH", "DELETE", "GET"])) throw new ApiKitException("The method $method is not supported", 405);
            $roughTokens = explode("/", $uri);
            foreach($roughTokens as $t) if(trim($t) != "") $tokens[] = $t;
            if(count($tokens) == 0) throw new ApiKitException("Entity not found in uri $uri");
            switch(count($tokens)) {
                case 1: //entity
                    $entity = strtolower($tokens[0]);
                    $action = "";
                    $keys = [];
                    break;
                case 2: //entity/action or //entity/?pk1={v1}[&pk2={v2}[...&pkN={vN}]]
                    $entity = strtolower($tokens[0]);
                    if(strpos($tokens[1], "?") !== FALSE) {
                        $s = str_replace("?", "", $tokens[1]);
                        $action = "";
                        $keys = $this->ParseQueryString($s);
                    } else {
                        $action = $tokens[1];
                        $keys = [];
                    }
                    break;
                case 3: //entity/action/?pk1={v1}[&pk2={v2}[...&pkN={vN}]]
                    $entity = strtolower($tokens[0]);
                    $action = strtolower($tokens[1]);
                    $s = str_replace("?", "", $tokens[2]);
                    $keys = $this->ParseQueryString($s);
                    break;
                default:
                    $entity = "";
                    $action = "";
                    $keys = [];
                    break;
            }
            $this->rp = new RouteParameters($sc, $lng, $method, $entity, $action, $keys);
        }

        private function ParseQueryString(string $qs) : array {
            $arr = [];
            $tokens = explode("&", $qs);
            foreach($tokens as $token) {
                $parts = explode("=", $token);
                if(count($parts) != 2) throw new ApiKitException("paramString $qs is not a valid parameter. This haven't two tokens.");
                $arr[$parts[0]] = $parts[1];
            }
            return $arr;
        }

        private static function Init() : void {
            //get params
            $uri = $_SERVER["REQUEST_URI"];
            $method = strtoupper($_SERVER["REQUEST_METHOD"]);
            $sc = isset($_SERVER["HTTP_X_SC"]) ? $_SERVER["HTTP_X_SC"] : "";
            $lng = isset($_SERVER["HTTP_X_LNG"]) ? $_SERVER["HTTP_X_LNG"] : "";
            $ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "";
            $ipProxy = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : "";
            //check syntax and logic error
            if ($uri == "/") {
                $errMsg = "This domain is reserved. The attempt to access without right credential has just tracked and you can't trying an action like this in future.";
                throw new ApiKitException($errMsg);
            }
            self::$Info = ["sc" => $sc, "lng" => $lng, "method" => $method, "uri" => $uri, "ip" => (trim($ip) != "" ? $ip : $ipProxy)];
        }

        public static function Start(callable $routeDelegate) : string {
            try {
                self::Init();
                $sc = self::$Info["sc"];
                $uri = self::$Info["uri"];
                $lng = self::$Info["lng"];
                $ip = self::$Info["ip"];
                $rm = new RouteManager();
                $result = call_user_func_array($routeDelegate, [$sc, $uri, $lng, $rm->rp]);
                return json_encode($result, true);
            } catch(Throwable $ex) { return json_encode(ApiResult::Ko($ex->getMessage(), $ex->getCode() ?: 400), true); }
        }
    }
?>