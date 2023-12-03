# Directory Structural Methods

In this section, you will learn how to manage directory hierarchy using the `Vinograd\SimpleFiles\Directory` class. This
class offers two types of methods: structural and application. Structural methods prepare an object model of directories
and files, while application methods are designed to transfer the model to disk.

> ❗ This section describes only structural methods; applied ones will be discussed in the following sections of this
> manual.

There are 4 main methods for preparing the directory object model:

- addDirectory
- removeDirectory
- addFile
- removeFile

It is important to note that all these methods do not work with a real file system, but only create a model of a tree of
files and directories that can later be written to disk, copied or deleted from disk.

## addDirectory method

This method takes as input one argument `Vinograd\SimpleFiles\AbstractDirectory` - this is the directory object that
needs
to be added as a child. The method returns exactly the object being added.

The system is designed in such a way that the same directory or file object cannot reside in more than one directory at
the same time. When you add a child element, it becomes a child of the object you are adding it to and is removed from
the list of children of the previous parent object.

Let's look at an example in which we need to make such a folder tree:

```
root
|___child1
    |___child2
        |___child3
```

Example code:

```php
<?php
require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

use Vinograd\SimpleFiles\Directory;

$root = new Directory('root');
$child1 = new Directory('child1');
$child2 = new Directory('child2');
$child3 = new Directory('child3');

$root->addDirectory($child1)->addDirectory($child2)->addDirectory($child3);
```

Our tree is ready.

> ❗ If you try to add a directory with a name that already exists among its children, it will throw a
> `Vinograd\IO\Exception\AlreadyExistException`. To check whether a child directory with a particular name exists, you
> can
> use the `containsDirectory` method, passing the directory name.
> ```php
> if(!$parent->containsDirectory($directory->getLocalName())){
>     $parent->addDirectory($directory);
> }
> ```

The `addDirectory` method can add its parent or even the root directory as a child directory without any problems. This
can best be demonstrated with an example.

Let's change the resulting structure so that the root element is the `child3` directory:

```php
$child3->addDirectory($root);
```

Modification result:

```
|-------------------------|--------------------------|
|          Было           |          Стало           |
|-------------------------|--------------------------|
|   root                  |    child3                |  
|   |___child1            |    |___root              |
|       |___child2        |        |___child1        |
|           |___child3    |            |___child2    |
|-------------------------|--------------------------|
```

Let's continue the manipulation and make the root directory `child1`.

Code:

```php
$child1->addDirectory($child3);
```

Now our tree will look like this:

```
child1
|___child2
|___child3
    |___root
```

The resulting result shows that the `$child1` object took part of the branch starting from the root directory up to
itself and added this branch to its children, after which it itself became the root directory.

## removeDirectory method

This method is designed to remove directories from the list of child ones.

> ❗ The directory to be removed from the list must be present in the list, otherwise the exception
> `Vinograd\SimpleFiles\Exception\TreeException` will be thrown.

Example of deleting a directory:

```php
<?php
require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

use Vinograd\SimpleFiles\Directory;

$root = new Directory('root');
$child1 = new Directory('child1');

$root->addDirectory($child1);

$root->removeDirectory($child1);
```

In the beginning we created two directory objects, then we added one directory to the other as a child, and in the last
line of code we deleted the newly added directory. As a result, both directories remained root directories with no
children.

## Methods For Adding And Removing Files

Methods for working with files have the same principle as methods for working with directories. It is important to note
that the `addFile` method has properties similar to the `addDirectory` method. An `AlreadyExistException` is thrown if
an
attempt is made to add a file with a name that already exists in child elements. Please note that a file cannot be in
multiple directories at the same time.

The `removeFile` method is similar to the `removeDirectory` method. If the file you want to delete is not in the list of
children, a `TreeException` is thrown.

Example of adding a file:

```php
<?php
require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

use Vinograd\SimpleFiles\Directory;

$root = new Directory('root');
$child1 = new Directory('child1');
$file1 = new File('file1.txt');
$file2 = new File('file2.txt');

$root->addDirectory($child1);

$root->addFile($file1);

$child1->addFile($file2);
```

In the example, we created two folders and two files, then nested one folder in another and added a file to each of
them.

The result of these manipulations is as follows:

```
root
|___child1
|   |___file2.txt
|___file1.txt
```

Let's delete files from both directories:

```php
$root->removeFile($file1);
$child1->removeFile($file2);
```

Result:

```
root
|___child1
```

The directories are empty, but the files are still there, and you can do some other manipulations with them.

[Table of contents](../../README.md#user-guide)