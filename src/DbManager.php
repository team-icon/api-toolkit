<?php
    namespace teamicon\apikit;
    use mysqli;
    use mysqli_stmt;
    use Throwable;

    abstract class DbManager {
        public const APEX_ESCAPE = "`";

        protected mysqli $Conn;
        protected string $host;
        protected string $usr;
        protected string $psw;
        protected string $dbName;
        protected int $InsertId;

        public function __construct(string $host, string $usr, string $psw, string $dbName) {
            $this->Conn = $this->GetInstance($host, $usr, $psw, $dbName);
        }

        private function GetInstance(string $host, string $usr, string $psw, string $dbName) : mysqli {
            try {
                $this->host = $host;
                $this->usr = $usr;
                $this->psw = $psw;
                $this->dbName = $dbName;
                return new mysqli($this->host, $this->usr, $this->psw, $this->dbName);
            } catch(Throwable $ex) {
                $errMsg = "An error occured when I was trying to open a connection to db $dbName. I received this error message " . $ex->getMessage();
                Logger::WriteError($errMsg);
                throw new ApiKitException($errMsg);
            }
        }

        public function GetDatabaseName() : string { return $this->dbName; }

        public function GetLastId() : int { return $this->InsertId; }

        public function Execute(string $query, string $types, array $params) : int {
            if($query == "") throw new ApiKitException("Query parameter is empty");
            if(!preg_match("/^(insert|update|delete).*$/i", $query)) throw new ApiKitException("Execute function has been called with a wrong query");
            if($this->Conn == null || !is_resource($this->Conn)) throw new ApiKitException("The connection with db is closed");
            $isInsert = preg_match("/^insert.*$/i", $query);
            $stmt = null;
            if($types == "" || count($params) == 0) throw new ApiKitException("Invalid parameters: types and params can't be empty");
            $stmt = $this->Conn->prepare($query);
            if($stmt) {
                $bind_names[] = $types;
                for($i = 0; $i < count($params); $i++) {
                    $bind_name = 'bind' . $i;
                    $$bind_name = $params[$i];
                    $bind_names[] = &$$bind_name;
                }
                call_user_func_array([$stmt, 'bind_param'], $bind_names);
                if(!$stmt->execute()) {
                    $note = $this->GetErrorMessage($stmt);
                    $stmt->close();
                    $this->Conn->close();
                    $errMsg = "The database " . static::GetDatabaseName() . " has crashed while it was lunching the query $query. Additional notes: ";
                    $errMsg .= $note != "" ? $note : "n.d.";
                    throw new ApiKitException($errMsg);
                }
                $rows = $this->Conn->affected_rows;
                if($isInsert) $this->InsertId = $this->Conn->insert_id;
                $stmt->close();
                $this->Conn->close();
                return $rows;
            } else {
                $note = $this->GetErrorMessage();
                $this->Conn->close();
                $errMsg = "The database " . static::GetDatabaseName() . " has crashed while it was lunching the query $query. Additional notes: ";
                $errMsg .= $note != "" ? $note : "n.d.";
                throw new ApiKitException($errMsg);
            }
        }

        public function Query(string $query, string $types = "", array $params = []) : array {
            $arr = [];
            if($query == "") throw new ApiKitException("Query is empty");
            if(!preg_match("/^select.*/i", $query)) throw new ApiKitException("Query is not a valid select statement");
            if($this->Conn == null || !is_resource($this->Conn)) throw new ApiKitException("The connection with db is closed");
            if($types == "" && count($params) == 0) { //standard query
                $res = $this->Conn->query($query);
                if($res->num_rows > 0) while($row = $res->fetch_assoc()) array_push($arr, $row);
                return $arr;
            } else { //query with statement
                if($types == "" || count($params) == 0) throw new ApiKitException("Request a select with statement without right parameters as input. Please check types and params fields");
                $stmt = $this->Conn->prepare($query);
                if($stmt) {
                    $bind_names[] = $types;
                    for($i = 0; $i < count($params); $i++) {
                        $bind_name = 'bind' . $i;
                        $$bind_name = $params[$i];
                        $bind_names[] = &$$bind_name;
                    }
                    call_user_func_array([$stmt, 'bind_param'], $bind_names);
                    if(!$stmt->execute()) {
                        $note = $this->GetErrorMessage($stmt);
                        $stmt->close();
                        $this->Conn->close();
                        $errMsg = "The database " . static::GetDatabaseName() . " has crashed while it was lunching the query $query. Additional notes: ";
                        $errMsg .= $note != "" ? $note : "n.d.";
                        throw new ApiKitException($errMsg);
                    }
                    if(!$result = $stmt->get_result()) {
                        $note = $this->GetErrorMessage($stmt);
                        $stmt->close();
                        $this->Conn->close();
                        $errMsg = "The database " . static::GetDatabaseName() . " has crashed while it was lunching the query $query. Additional notes: ";
                        $errMsg .= $note != "" ? $note : "n.d.";
                        throw new ApiKitException($errMsg);
                    }
                    while($row = $result->fetch_assoc()) array_push($arr, $row);
                    $stmt->close();
                    $this->Conn->close();
                    return $arr;
                } else {
                    $note = $this->GetErrorMessage();
                    $this->Conn->close();
                    $errMsg = "The database " . static::GetDatabaseName() . " has crashed while it was lunching the query $query. Additional notes: ";
                    $errMsg .= $note != "" ? $note : "n.d.";
                    throw new ApiKitException($errMsg);
                }
            }
        }

        public function Scalar(string $query, string $types = "", array $params = []) {
            $res = self::Query($query, $types, $params);
            if(count($res) == 0) throw new ApiKitException("Select statement hasn't returned anything");
            $keys = array_keys($res);
            $firstKey = $keys[0];
            if(is_array($res[$firstKey])) {
                $innerKeys = array_keys($res[$firstKey]);
                $firstInnerKey = $innerKeys[0];
                return $res[$firstKey][$firstInnerKey];
            }
            return $res[$firstKey];
        }

        public function GetErrorMessage(mysqli_stmt $stmt = null) : string {
            $dbErr = !is_null($this->Conn) ? $this->Conn->error : "";
            if($dbErr == "") $dbErr = "n.d.";
            $stmtErr = !is_null($stmt) ? $stmt->error : "";
            if($stmtErr == "") $stmtErr = "n.d.";
            $connErr = !is_null($this->Conn) ? $this->Conn->connect_error : "";
            if($connErr == "") $connErr = "n.d.";
            return "The creation of statement is failed with db error $dbErr and stmt $stmtErr and connection error $connErr";
        }
    }
?>