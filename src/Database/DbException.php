<?php
    namespace TeamIcon\TeamIconApiToolkit\Database;
    use \TeamIcon\Exceptions\CustomException;

    require_once(__DIR__ . "/../Exceptions/CustomException.php");

    class DbException extends CustomException {
        protected string $dbName;
        protected string $query;
        protected string $note;

        public function __construct(string $dbName, string $q, string $note = "", int $httpCode = 400) {
            $msg = "In $dbName database throw an exception occured when I've tryed to execute query: $q" . ($note != "" ? ", $note" : "");
            parent::__construct($msg, $httpCode);
            $this->dbName = $dbName;
            $this->query = $q;
            $this->note = $note;
        }

        public function GetDatabaseName() : string { return $this->dbName; }
        public function GetQuery() : string { return $this->query; }
        public function GetNote() : string { return $this->note; }
    }
?>