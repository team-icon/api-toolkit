<?php
    namespace teamicon\apikit\Traits;
    use DateTime;
    use DateTimeZone;
    use \teamicon\apikit\Database\EnumDbFieldType;
    use \teamicon\apikit\Exceptions\{InvalidArgumentException, NullReferenceException, InvalidTypeException};
    use Throwable;

    trait CheckFieldAssignament {
        protected function CheckFieldAssignament(string $fieldName, $fieldValue, int $fieldType, int $fieldMaxLenght = 0, bool $allowNull = false) : bool {
            if($allowNull && $fieldValue == null) throw new NullReferenceException($fieldName, $fieldType, "null value doesn't allowed");
            switch($fieldType) {
                case EnumDbFieldType::BOOL:
                    if(!($allowNull && is_null($fieldValue)) || !is_bool($fieldValue) || !(is_int($fieldValue) && (int)$fieldValue >= 0 && (int)$fieldValue <= 1)) {
                        new InvalidTypeException($fieldName, gettype($fieldValue), "bool");
                        return false;
                    }
                    return true;
                case EnumDbFieldType::UINT:
                    if(!($allowNull && is_null($fieldValue)) || !is_int($fieldValue) || (int)$fieldValue < 0) {
                        $note = is_int($fieldValue) && (int)$fieldValue < 0 ? "$fieldName is less than zero ($fieldValue)" : "";
                        new InvalidTypeException($fieldName, gettype($fieldValue), "uint", $note);
                        return false;
                    }
                    return true;
                case EnumDbFieldType::INT:
                    if(!($allowNull && is_null($fieldValue)) || !is_int($fieldValue)) {
                        new InvalidTypeException($fieldName, gettype($fieldValue), "int");
                        return false;
                    }
                    return true;
                case EnumDbFieldType::FLOAT:
                    if(!($allowNull && is_null($fieldValue)) || !is_float($fieldValue) || !is_double($fieldValue)) {
                        new InvalidTypeException($fieldName, gettype($fieldValue), "float");
                        return false;
                    }
                    return true;
                case EnumDbFieldType::UFLOAT:
                    if(!($allowNull && is_null($fieldValue)) || !(is_float($fieldValue) || is_double($fieldValue)) || (float)$fieldValue < 0) {
                        $note = (is_float($fieldValue) || is_double($fieldValue)) || (float)$fieldValue < 0 ? "$fieldName is less than zero ($fieldValue)" : "";
                        new InvalidTypeException($fieldName, gettype($fieldValue), "ufloat", $note);
                        return false;
                    }
                    return true;
                case EnumDbFieldType::DATETIME:
                    if(!($allowNull && is_null($fieldValue)) || !is_string($fieldValue) || strlen($fieldValue) != 19) {
                        new InvalidTypeException($fieldName, gettype($fieldValue), "datetime");
                        return false;
                    }
                    try { DateTime::createFromFormat("Y-m-d H:i:s", $fieldValue, new DateTimeZone("Europe\Rome")); }
                    catch(Throwable $ex) { return false; }
                    return true;
                case EnumDbFieldType::DATE:
                    if(!($allowNull && is_null($fieldValue)) || !is_string($fieldValue) || strlen($fieldValue) != 10) {
                        new InvalidTypeException($fieldName, gettype($fieldValue), "date");
                        return false;
                    }
                    try { DateTime::createFromFormat("Y-m-d", $fieldValue, new DateTimeZone("Europe\Rome")); }
                    catch(Throwable $ex) { return false; }
                    return true;
                case EnumDbFieldType::STRING:
                    if(!($allowNull && is_null($fieldValue)) || !is_string($fieldValue) || ($fieldMaxLenght > 0 && strlen($fieldValue) != $fieldMaxLenght)) {
                        $note = "";
                        if(is_string($fieldValue) && $fieldMaxLenght > 0 && strlen($fieldValue) != $fieldMaxLenght)
                            $note = "Lenght (" . strlen($fieldValue) . " exceeds the max length value of $fieldMaxLenght";
                        new InvalidTypeException($fieldName, gettype($fieldValue), "string", $note);
                        return false;
                    }
                    return true;
                case EnumDbFieldType::TEXT:
                    if(!($allowNull && is_null($fieldValue)) || !is_string($fieldValue)) {
                        new InvalidTypeException($fieldName, gettype($fieldValue), "text");
                        return false;
                    }
                    return true;
                default:
                    new InvalidArgumentException("fieldType", $fieldType, "method there isn't in EnumDbFieldType.php");
                    return false;
            }
        }
    }
?>