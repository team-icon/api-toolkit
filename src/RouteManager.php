<?php
    namespace TeamIcon\TeamIconApiToolkit;
    use \TeamIcon\Exceptions\{CustomException, InvalidArgumentException};

    require_once(__DIR__ . "/RouteParameters.php");
    require_once(__DIR__ . "/Exceptions/CustomException.php");
    require_once(__DIR__ . "/Exceptions/InvalidArgumentException.php");

    final class RouteManager {
        public static array $Info = [];
        private RouteParameters $rp;

        private function __construct(array $listOfAllowedEntities) {
            $tokens = [];
            $sc = self::$Info["sc"];
            $lng = self::$Info["lng"];
            $uri = self::$Info["uri"];
            $method = self::$Info["method"];
            if($uri == "" || $uri == "/") throw new InvalidArgumentException("uri", $uri, "Attemp to crack system detected");
            if(count($listOfAllowedEntities) == 0) throw new InvalidArgumentException("count(listOfAllowedEntities)", count($listOfAllowedEntities), "is empty");
            if(!in_array(strtoupper($method), ["POST", "PATCH", "DELETE", "GET"]))
                throw new InvalidArgumentException("method", $method, "Method $method not supported", 405);
            $roughTokens = explode("/", $uri);
            foreach($roughTokens as $t) if(trim($t) != "") $tokens[] = $t;
            if(count($tokens) == 0) throw new CustomException("Entity not found in uri");
            if(!in_array(strtolower($tokens[0]), $listOfAllowedEntities))
                throw new InvalidArgumentException("tokens[0]", $tokens[0], "Entity $tokens[0] is not supported", 501);
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
                if(count($parts) != 2) throw new InvalidArgumentException("numof parts", count($parts), "paramString $qs is not a valid parameter");
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
                Logger::WriteAnomaly("Attempt to access at url $uri with method $method by ip $ip ($ipProxy)");
                $errMsg = "This domain is reserved. The attempt to access without right credential has just tracked and you can't trying an action like this in future.";
                throw new CustomException($errMsg);
            }
            //main logic of routing
            Logger::WriteUri($sc, $method, $uri);
            self::$Info = ["sc" => $sc, "lng" => $lng, "method" => $method, "uri" => $uri, "ip" => (trim($ip) != "" ? $ip : $ipProxy)];
        }

        public static function Start(array $listOfAllowedEntities, callable $initCheckDelegate, callable $routeDelegate) : string {
            try {
                self::Init();
                $sc = self::$Info["sc"];
                $uri = self::$Info["uri"];
                $lng = self::$Info["lng"];
                $ip = self::$Info["ip"];
                call_user_func_array($initCheckDelegate, [$ip]);
                $rm = new RouteManager($listOfAllowedEntities);
                $result = call_user_func_array($routeDelegate, [$sc, $uri, $rm->rp]);
                return json_encode($result, true);
            } catch(\Throwable $ex) { return json_encode(ApiResult::Ko($ex->getMessage(), $ex->getCode() ?: 400), true); }
        }
    }
?>