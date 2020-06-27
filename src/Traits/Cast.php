<?php
    namespace teamicon\apikit\Traits;

    use ReflectionObject;

    trait Cast {
        public function cast($reqUrl, $destinationClass, $sourceObject) {
            if(file_exists($reqUrl)) require_once($reqUrl);
            $destination = new $destinationClass();
            $sourceReflection = new ReflectionObject($sourceObject);
            $destinationReflection = new ReflectionObject($destination);
            $sourceProperties = $sourceReflection->getProperties();
            foreach ($sourceProperties as $sourceProperty) {
                $sourceProperty->setAccessible(true);
                $name = $sourceProperty->getName();
                $value = $sourceProperty->getValue($sourceObject);
                if ($destinationReflection->hasProperty($name)) {
                    $propDest = $destinationReflection->getProperty($name);
                    $propDest->setAccessible(true);
                    $propDest->setValue($destination,$value);
                } else $destination->$name = $value;
            }
            return $destination;
        }
    }
?>