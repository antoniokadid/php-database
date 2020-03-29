# WAPPKit Core - MySQL
A PHP library to define collections of objects.

Part of Web Application Kit (WAPPKit) Core which powers WAPPKit, a privately owned CMS.

*Project under development and may be subject to a lot of changes. Use at your own risk.*

## Installation

```bash
composer require antoniokadid/wappkit-core-mysql
```

## Requirements
* PHP 7.1
* PDO Extension
* MySQL Improved Extension

## Examples

```php
use AntonioKadid\WAPPKitCore\Database\MySQL\Connections\PdoConnection;
use AntonioKadid\WAPPKitCore\Database\MySQL\Exceptions\MySQLException;

try
{
    $connection = new PdoConnection('127.0.0.1', 3306, 'testDb', 'user', 'pass');

    $connection->execute('UPDATE test_table SET test_column = ? WHERE test_column = ?', ['newValue', 'oldValue']);

    $connection->commit();
}
catch(MySQLException $exception)
{
    $message = $exception->getMessage();
    $sqlQuery = $exception->getQuery();
    $sqlParameters = $exception->getParameters();
}
```

## LICENSE

MIT license.
