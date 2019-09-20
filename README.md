# php-mysql
A WYSIWYG library that can be used as a database connection layer.

*Project under development and may be heavily change. Use at your own risk.*

## Installation

```bash
composer require antoniokadid/php-mysql
```

## Requirements
* PHP 7.1
* PDO Extension
* MySQL Improved Extension

## Examples

```php
use AntonioKadid\MySql\PdoConnection;
use AntonioKadid\MySql\DatabaseException;

try
{
    $connection = new PdoConnection('127.0.0.1', 3306, 'testDb', 'user', 'pass');

    $connection->execute('UPDATE test_table SET test_column = ? WHERE test_column = ?', ['newValue', 'oldValue']);

    $connection->commit();
}
catch(DatabaseException $exception)
{
    $message = $exception->getMessage();
    $sqlQuery = $exception->getQuery();
    $sqlParameters = $exception->getParameters();
}
```

## LICENSE

php-mysql is released under MIT license.
