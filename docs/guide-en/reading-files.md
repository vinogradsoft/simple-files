# Reading files

In this section, we will learn about the process of reading data from files. Let's look at the reading method and learn
how to handle errors that occur when reading data from files.

## Receiving Data

To get data from a file, you must first execute the `read()` method. This method does not return anything; it simply
reads them from a file. Then you can get them using the `getContent()` method;

An example of getting data from a file:

```php
<?php
require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

use Vinograd\SimpleFiles\File;

$file = File::createBinded('path/to/file.txt');
$file->read();
echo $file->getContent(); 
```

An example of retrieving data from a file in the case of lazy linking to the file system:

```php
<?php
require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

use Vinograd\SimpleFiles\File;

$file = new File('file.txt');
$file->bindWithFilesystem('path/to');
$file->read();
echo $file->getContent(); 
```

## Error Processing

Before calling the `read()` method, the object must be associated with the file system. If called without communication,
a `\LogicException` will be thrown. You can check whether there is a connection using the `isBinded()` method; the
method will return `true` if there is a connection, if there is no connection - `false`.

An example in which a `\LogicException` will be thrown:

```php
$file = new File('file.txt');
try{
    $file->read();
}catch (\LogicException $e){
    echo $e->getMessage();
}
```

If, in the logic of the file handling program, the associated file is deleted (not by library methods) in the interval
between calling the `bindWithFilesystem(...)` method and the `read()` method, an exception
`\Vinograd\IO\Exception\NotFoundException` may occur.

An example that will result in the exception `\Vinograd\IO\Exception\NotFoundException` being thrown:

```php
$file = new File('file.txt');
$file->bindWithFilesystem('path/to');
\unlink('path/to/file.txt');
try{
    $file->read();
}catch (\Vinograd\IO\Exception\NotFoundException $e){
    echo $e->getMessage();
}
```

[Table of contents](../../README.md#user-guide)