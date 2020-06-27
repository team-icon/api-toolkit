<?php
    namespace TeamIcon\TeamIconApiToolkit\Database;

    abstract class EnumDbFieldType {
        public const BOOL = 0;
        public const UINT = 1;
        public const INT = 2;
        public const FLOAT = 3;
        public const UFLOAT = 4;
        public const DATETIME = 5;
        public const DATE = 6;
        public const STRING = 7;
        public const TEXT = 8;
    }
?>