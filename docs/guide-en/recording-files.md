# Recording files

In this section, we will look at two ways to record data. Let's look at all the intricacies of working with recording
files so that you can confidently operate with them in your projects.

The `Vinograd\SimpleFiles\File` class implements two methods for writing data to the file system: `write()`
and `writeTo(string $directoryPath)`. These methods differ in that the first uses the path of the source with which it
is associated for recording, and the second method writes to the specified directory.

## write method

> ❗ This method is intended for objects that have established a connection with the file system. If you run this method
> before binding, a `Compass\Exception\InvalidPathException` will be thrown.

An example when the file exists and new data needs to be written to it:

```php
$file = File::createBinded('/path/to/file.txt');
$file->setContent('change content');
$file->write();
```

Example when the file has not yet been created in the file system:

```php
$file = new File('file.txt');
$file->bindWithFilesystem('path/to/directory');
$file->setContent('data');
$file->write();
```

When executing the `$file->bindWithFilesystem('path/to/directory');` method, if a file exists at
`path/to/directory/file.txt`, the system will bind to it, if not, it will create an empty file.

> ❗ The `Vinograd\SimpleFiles\File` class has a `setLocalName` method. This method changes the file name. His work is a
> little specific. If you create a file not associated with the file system using the `new` operator, then before
> connecting with the file system you can change its name using this method. If you execute this method after linking to
> the file system, the value that was set by this method will only be used in the file copy and move methods so that
> these operations can be performed on the new file names.


An example of changing a file name before associating it with the file system:

```php
$file = new File('file.txt');
$file->setLocalName('fileRenamed.txt');
$file->bindWithFilesystem('path/to/directory');
$file->setContent('data');
$file->write();
```

In this case, the file will have the path `path/to/directory/fileRenamed.txt`. If we had executed the `setLocalName`
method after the `bindWithFilesystem` method, the data would have been written to the file `path/to/directory/file.txt`.

## writeTo method

> 📢 The `writeTo` method does not need to be previously associated with the file system.
> The directory in which the file is written must exist, otherwise a `Compass\Exception\InvalidPathException` will be
> thrown.

An example of use before binding to the file system.

```php
$file = new File('file.txt');
$file->setContent('content');
$file->writeTo('path/to/directory');
$file->writeTo('path/to/another/directory');
```

In this example, data will be written to two files that did not exist before being written: `path/to/directory/file.txt`
and `path/to/another/directory/file.txt`.

An example of method execution after binding to the file system.

```php
$file = File::createBinded('/path/to/file.txt');
$file->read();
$file->setContent('change '. $file->getContent());
$file->writeTo('path/to/directory');
```

In this example, we created an object associated with the file system along the path `/path/to/file.txt`, then read its
contents, added the word “change” to the beginning of the contents using the `setContent` method and then wrote the
changed file `file. txt` to the `path/to/directory` directory. As a result, a file `path/to/directory/file.txt` was
created with modified contents.

An example of executing the method after linking to the file system and changing the name of the file being written.

```php
$file = File::createBinded('/path/to/file.txt');
$file->read();
$file->setContent('change '. $file->getContent());
$file->setLocalName('fileRenamed.txt');
$file->writeTo('path/to/directory');
```

We did the same as in the previous example, only we changed its name before recording to “fileRenamed.txt”.
Result: `path/to/directory/fileRenamed.txt`.

> ❗ **Important note!**<br>
> After executing the `writeTo` method, the connection to the file system does not change, the source remains the same.

[Table of contents](../../README.md#user-guide)