# General Information

Structural and applied methods are the two main categories of methods used in this library. Structural methods prepare
an object model of directories and files, defining the structure and relationships between them. Application methods, on
the other hand, interact with the file system and are designed to transfer the model to disk.

The created model can include directories and files that already exist in the file system, as well as those that have
not yet been created. Essentially, a model is what should end up on disk as a result of using application methods,
excluding the `delete()` method, which deletes the created object model from disk.

To build a file system model, the library contains two types of objects: file (`Vinograd\SimpleFiles\File`) and
directory (`Vinograd\SimpleFiles\Directory`). A file in working with a file system is responsible only for itself; in
other words, it writes, copies, moves and deletes only itself. When working with a file system, a directory is
responsible not only for itself, but also for its child elements. So, for example, by launching the copy method for a
directory, the copy methods for all child elements will be launched.

In general, objects that model a file system have two states: when the object is not associated and when the object is
associated with the file system. The bonded to the file system is determined by the presence of a path to the
file/directory in the object.

In the case where an object is associated with a file system, it has two paths:

1. Path to a file/directory in the file system.
2. A path that defines the position of an object in the model.

The path to the file system can be obtained using the `getPath()` method, and the path in the object model using the
`getLocalPath()` method. The `getPath()` method returns a `Compass\Path` object, and the `getLocalPath()` method returns
a path string.

> 📢 The `Compass\Path` object is an object representation of the path string to a file; you can read more about it in
> the
> documentation for the [vinogradsoft/compass](https://github.com/vinogradsoft/compass#path-component) library.

## Options For Creating File And Directory Objects

Object creation is possible in both states. The process for creating file and directory objects is identical.

To create an object without a file system link, use the `new` statement. It is important to
understand that you only need to specify the name of the file or folder, and not the path to an existing file. If you do
try to specify a path, the system will throw a `LogicException`. A similar exception will be thrown if an empty string
is passed. For everything to work correctly, you only need to specify the name.

Example:

```php
// File
$file = new File('myFileName.txt');
// Directory
$directory = new Directory('myDirectoryName');
```

Creating an object with a file system connection requires using the static method `createBinded(string $path)`, which
passes the file path as a string as its argument.

```php
// File
$file = File::createBinded('/var/www/myFileName.txt');
// Directory
$directory = Directory::createBinded('/var/www');
```

If the file or directory at the passed path does not exist, the exception `\Vinograd\IO\Exception\NotFoundException`
will be thrown. By default, files and directories use the `\Vinograd\SimpleFiles\DefaultFilesystem` class to work with a
physical file system, and this file system is local. You can pass both absolute and relative paths to the create method.
The rule is simple - `DefaultFilesystem` internally uses the php function `realpath(...)`. All returns of false by this
function will throw an exception (see the [realpath](https://www.php.net/manual/en/function.realpath.php) documentation
for when this function may return `false`).

## File system binding

You can bind a new object to a file system using the `bindWithFilesystem(string $path)` method, which takes the path
string as an argument. You may need to associate a new object with the file system if you want to bind based on some
condition. The internal processes for binding directory and file objects are different, but both types of object have
the same method of checking for binding to the file system - the `isBinded` method. The `isBinded` method returns a
boolean value.

### Directory binding

```php
$directory = new Directory('root');
$directory->bindWithFilesystem('/var/www');
```

In the example above, the directory will be created if it is not in the `/var/www` directory and will have the source
path `/var/www/root`. If the directory already exists, the same thing will happen, but without creating the directory.

### Linking files

```php
$file = new File('file.txt');
$file->bindWithFilesystem('/var/www');
```

The file will be created in the `/var/www` directory, provided that it does not already exist at the specified path
`/var/www/file.txt`. If the file already exists in the specified directory, the system will simply link it without
overwriting it. It is important to note that when you bind a file system object, no data is written. This is done so
that the data can later be read from the specified source.

The path to which the object is associated must exist. It is important that the file or directory name is not included
at the end of the path when linking.

❌ Here are examples of INCORRECT usage:

- for directories - `$directory->bindWithFilesystem('/var/www/root')`
- for files - `$file->bindWithFilesystem('/var/www/file.txt')`

> ❗ Linking new file system objects is optional. For example, you can copy, move, and write files and directories that
> are not associated with the file system.

[Table of contents](../../README.md#user-guide)