# Copying Files

In this section, we will look at the file copying method and error handling. The method is very simple to use, so it is
easier to explain how to use it with examples.

## Examples

> ❗ The copy method does not require that the object be associated with the file system. It allows you to change the
> destination file name using the `setLocalName` method.

Make three copies of the existing file in different directories:

```php
$file = File::createBinded('path/to/file.txt');
$file->copy('path/to/directory1'); # path/to/directory1/file.txt
$file->copy('path/to/directory2'); # path/to/directory2/file.txt
$file->copy('path/to/directory3'); # path/to/directory3/file.txt
```

Make two copies of an existing file in different directories and with different names:

```php
$file = File::createBinded('path/to/file.txt');
$file->copy('path/to/directory1'); # path/to/directory1/file.txt
$file->setLocalName('fileRenamed.txt');
$file->copy('path/to/directory2'); # path/to/directory2/fileRenamed.txt
```

Make a copy of a file that does not exist in the file system:

```php
$file = new File('file.txt');
$file->setContent('content');
$file->copy('path/to/directory1');  # path/to/directory1/file.txt
```

Make two copies of a non-existent file in different directories and with different names:

```php
$file = new File('file.txt');
$file->setContent('content');
$file->copy('path/to/directory1'); # path/to/directory1/file.txt
$file->setLocalName('fileRenamed.txt');
$file->copy('path/to/directory2'); # path/to/directory2/fileRenamed.txt
```

## Error handling

When copying files, it is important that the destination directory exists. The following example will throw the
exception `Compass\Exception\InvalidPathException`

```php
$file = File::createBinded('path/to/file.txt');
try{
   $file->copy('not/exist/directory/path');
}catch (\Compass\Exception\InvalidPathException $e){
   echo $e->getMessage();
}
```

[Table of contents](../../README.md#user-guide)