<?php
    namespace teamicon\apikit\Database;
    use mysqli;
    use mysqli_stmt;
    use \teamicon\apikit\Exceptions\CustomException;
    use Throwable;

    require_once(__DIR__ . "/DbException.php");
    require_once(__DIR__ . "/../Exceptions/CustomException.php");

    abstract class DbManager {
        public const APEX_ESCAPE = "`";

        protected static int $InsertId;

        protected mysqli $Conn;

        public function __construct() { $this->Conn = static::GetIstance(); }

        abstract protected static function GetDatabaseName() : string;
        abstract protected static function GetIstance() : mysqli;

        public function GetLastId() : int { return self::$InsertId; }

        public function Execute(string $query, string $types, array $params) : int {
            if($query == "") throw new CustomException("Query parameter is empty");
            if(!preg_match("/^(insert|update|delete).*$/i", $query)) throw new CustomException("Execute function has been called with a wrong query");
            try { if($this->Conn == null || !is_resource($this->Conn)) $this->Conn = static::GetIstance(); }
            catch(Throwable $ex) { $this->Conn = static::GetIstance(); }
            $isInsert = preg_match("/^insert.*$/i", $query);
            $stmt = null;
            if($types == "" || count($params) == 0) throw new CustomException("Invalid parameters: types and params can't be empty");
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
                    throw new DbException(static::GetDatabaseName(), $query, $note);
                }
                $rows = $this->Conn->affected_rows;
                if($isInsert) self::$InsertId = $this->Conn->insert_id;
                $stmt->close();
                $this->Conn->close();
                return $rows;
            } else {
                $note = $this->GetErrorMessage();
                $this->Conn->close();
                throw new DbException(static::GetDatabaseName(), $query, $note);
            }
        }

        public function Query(string $query, string $types = "", array $params = []) : array {
            $arr = [];
            if($query == "") throw new CustomException("Query is empty");
            if(!preg_match("/^select.*/i", $query)) throw new CustomException("Query is not a valid select statement");
            try { if($this->Conn == null || !is_resource($this->Conn)) $this->Conn = static::GetIstance(); }
            catch(Throwable $ex) { $this->Conn = static::GetIstance(); }
            if($types == "" && count($params) == 0) { //standard query
                $res = $this->Conn->query($query);
                if($res->num_rows > 0) while($row = $res->fetch_assoc()) array_push($arr, $row);
                return $arr;
            } else { //query with statement
                if($types == "" || count($params) == 0) throw new CustomException("Request a select with statement without right parameters as input. Please check types and params fields");
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
                        throw new DbException(static::GetDatabaseName(), $query, $note);
                    }
                    if(!$result = $stmt->get_result()) {
                        $note = $this->GetErrorMessage($stmt);
                        $stmt->close();
                        $this->Conn->close();
                        throw new DbException(static::GetDatabaseName(), $query, "Getting result is failed. $note");
                    }
                    while($row = $result->fetch_assoc()) array_push($arr, $row);
                    $stmt->close();
                    $this->Conn->close();
                    return $arr;
                } else {
                    $note = $this->GetErrorMessage();
                    $this->Conn->close();
                    throw new DbException(static::GetDatabaseName(), $query, $note);
                }
            }
        }

        public function Scalar(string $query, string $types = "", array $params = []) {
            $res = self::Query($query, $types, $params);
            if(count($res) == 0) throw new CustomException("Select statement hasn't returned anything");
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