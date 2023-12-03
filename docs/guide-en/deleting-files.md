# Deleting Files

To delete files, the `Vinograd\SimpleFiles\File` class has the `delete()` method. It is aimed exclusively at deleting
files located in the file system. This method cannot delete files that are not associated with the file system. Calling
this method on an object that is not associated with the file system will result in a `\LogicException` being thrown. To
avoid such situations, you should be careful about creating unnecessary files in your code.

Example of deleting a file:

```php
$file = File::createBinded('path/to/file.txt');
$file->delete();
```

In cases where an object was successfully associated with the file system, but was deleted by a third-party system
before calling the `delete()` method, the exception `\Vinograd\IO\Exception\NotFoundException` will be thrown as shown
in this example:

```php
$file = File::createBinded('path/to/file.txt');
\unlink('path/to/file.txt');
try{
    $file->delete();
}catch (\Vinograd\IO\Exception\NotFoundException $exception){
    echo $exception->getMessage();
}
```

[Table of contents](../../README.md#user-guide)