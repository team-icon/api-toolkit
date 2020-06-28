# Team Icon ApiKit v.1.1 stable
This library is a simple toolkit to help Team icon's developer to create a native php API.
## Installation
You need to use a [composer](https://getcomposer.org/) installation to add this library on your application. 
Open a terminal or a command prompt and digit:

 ```bash
composer require teamicon/apikit:*
```

after some minutes it should appear a vendor folder and a composer.json file in the root.

## Usage
In the application you'll need to write as first line of your script:
```php
require_once(__DIR__ . "/your-path/vendor/autoload.php");
use \teamicon\apikit\className;
$foo = new className();
...
```

the your-path token is the right path where the folder vendor is located. For instance if your script is put in the sources folder in the root, your path becomes ../ because you need to browse back.

```php
require_once(__DIR__ . "/../vendor/autoload.php");
``` 

## The body of the index
Creating an API application is simple but required some features for working well. In the body we need to put the references of the scripts which will use in the application, after we'll put the headers required and finally we can use the apikit to manage fast all route rules.

```php
require_once(__DIR__. "/vendor/autoload.php");
use \teamicon\apikit\{list of classes that you will use separated by comma}
...
//header for CORS calls
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PATCH, DELETE, OPTIONS");
header("Access-Control-Max-Age: 100");
//header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: X-SC, X-LNG, AccountKey,x-requested-with, Content-Type, origin, authorization, accept, client-security-token, host, date, cookie, cookie2");
header("Content-Type: application/json");
//preflight test
if ($_SERVER['REQUEST_METHOD'] != 'OPTIONS') echo RouteManager::Start( 'route');
function route(string $sc, string $url, RouteParameters $rp) {
    //put here your logic
}
```  

## List of classes in the apikit
### ApiKitException
This class is inherit from Exception and it will use to identify internal exception.
### ApiResult 
It's used to create a standard response.
### DbManager
It's a abstract class to help user with common functions with the use of a db mysql and derivated.
You can create your derivated class following this sample:

```php
<?php
    namespace yourNamespace;
    require_once __DIR__ . "/../../vendor/autoload.php";
    use \teamicon\apikit\DbManager;

    final class MyDbManager extends DbManager {
        private const Connection = ["HOST" => "***", "NAME" => "***", "USR" => "***", "PSW" => "***"];
        public function __construct() { parent::__construct(); }
        protected static function GetDatabaseName(): string { return "your database name"; }
        protected static function GetIstance() : \mysqli { return new \mysqli(self::Connection["HOST"], self::Connection["USR"], self::Connection["PSW"], self::Connection["NAME"]); }
    }
?>
``` 
and use it like:
```php
namespace yourNamespace;
$res = MyDbManager::Query("SELECT usr, active FROM user WHERE id = ?", "i", ["id" => 123]);
$active = $res[0]["active"];
```
### Logger
It's use to log some activities and error in the specific file.
### RouteManager
It's the core of the apikit and we had discussed before about you can use it.
### RouteParameters
It's an internal class to exchange info about routing.
### Utility
It contains some tips to simplyfy che life of the developer.
