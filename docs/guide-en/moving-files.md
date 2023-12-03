# Moving Files

When working with files, one of the key actions is to move them. This process can include both files that already exist
in the file system and those that are temporarily stored in memory. It is important to remember that the path to the
directory where the file will be moved must exist. Otherwise, the appropriate exception will be raised. It is necessary
to pay attention to the fact that, unlike the `copy` and `writeTo` methods, after moving the file, the communication
path with the source changes to the one that resulted from the move.

## Moving Files That Exist On The File System

To move an existing file you can use the following code:

```php
$file = File::createBinded('path/to/file.txt');
$file->move('path/to/new/directory'); # path/to/new/directory/file.txt
```

This code creates an instance of the `Vinograd\SimpleFiles\File` class with the file path and moves it to the specified
directory using the `move()` method. In this case, the connection with the source will no longer
be `'path/to/file.txt'`, but `path/to/new/directory/file.txt`.

If we want to move a file and change its name in the destination directory, we can use the `setLocalName` method which
will change the name of the destination file.

Sample code:

```php
$file = File::createBinded('path/to/file.txt');
$file->setLocalName('fileRenamed.txt');
$file->move('path/to/new/directory'); # path/to/new/directory/fileRenamed.txt
```

## Moving Files That Are In Memory

After moving files that are in memory, a connection with the file system is established.

Example:

```php
$file = new File('file.txt');
$file->setContent('content');
$file->move('path/to/new/directory');  # path/to/new/directory/file.txt
```

Moving a file while changing the file name:

```php
$file = new File('file.txt');
$file->setContent('content');
$file->setLocalName('fileRenamed.txt');
$file->move('path/to/new/directory'); # path/to/new/directory/fileRenamed.txt
```

### Error processing

If the directory into which the file is moved does not exist, a `Compass\Exception\InvalidPathException` exception will
be thrown.

Example:

```php
$file = File::createBinded('path/to/file.txt');
try{
   $file->move('not/exist/directory/path');
}catch (\Compass\Exception\InvalidPathException $exception){
   echo $exception->getMessage();
}
```

An exception will also be thrown for moved files that are in memory.

Example:

```php
$file = new File('file.txt');
$file->setContent('content');
try{
   $file->move('not/exist/directory/path');
}catch (\Compass\Exception\InvalidPathException $exception){
   echo $exception->getMessage();
}
```

[Table of contents](../../README.md#user-guide)