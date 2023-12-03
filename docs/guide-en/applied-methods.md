# Applied Methods

In this section we will look at applied methods. In total, four main methods are implemented for working with the file
system: `copy()`, `move()`, `writeTo()` and `delete()`.

Examples using only applied methods are not very informative because they cannot be used without a generated object
model.

To demonstrate the interaction of the object model and the file system, a project was created
with [examples](https://github.com/vinogradsoft/example) for all applied methods.

At the end of this section, the concept of a fictitious application is described, which we will refer to when posing the
problem for examples in the following sections of this guide. The examples will create console commands close to reality
for operations of copying, moving, writing and deleting files and directories.



> 📢 To make the examples clearer, they do not include checks that are typically performed in code. Instead, the examples
> focus on creating a file system model and writing it to disk.

## Description of methods

### 👉 copy

This method copies the file system object model to disk. The copy process goes like this: Once the object model is
ready, the `copy` method is called on the root directory with the destination path as an argument. After this, all files
that are associated with the file system are read and written to the destination. Non-filesystem files write content
that you specify in your program to disk.

The result of the copying will be the created files and folders on the disk. The entire created structure on the disk
will repeat the location of files and directories of the object model. The connections between directory objects and
model files will remain the same.

Possible errors in the copying process are usually associated with specifying a non-existent destination path or lack of
write rights to the target directory, in both cases a `Vinograd\IO\Exception\IOException` exception is generated.

> 🔋 Example of using the [copy()](copying-directories.md) method.

### 👉 writeTo

The `writeTo()` method writes the current state of the object model to the specified location, similar to the `copy()`
method, but with one difference: the associated files are not read, but written "as is". All other characteristics of
these methods are identical.

> 🔋 Example of using the [writeTo()](writing-directories.md) method.

### 👉 move

The `move` method moves files and directories based on the object model.

Moving is done as follows: the `move()` method is called on the root directory in the object model, specifying the path
to the destination. Then it starts copying all files and folders to the specified location. Once the copying is
complete, the original files and folders are deleted from the disk. Deleting from disk only affects those files and
directories that were associated with objects in the model.

As a result of the move, new files and folders are created in the specified location, and the original ones are deleted
from the disk if they are present in the object model. The structure at the destination follows that of the model. All
directory and file objects will be associated with the file system at the destination.

Possible errors when moving are usually associated with specifying a non-existent path to the destination or lack of
permission to write to the specified directory. In such cases, the exception `Vinograd\IO\Exception\IOException` is
thrown.

The main error is the absence of a file object in the model, which exists on disk in one of the relocatable directories
of the object model. If the file has not been added to the object model, the system will not be able to delete it, as it
will assume that the file does not exist. This will lead to the removal of a non-empty directory and, as a result, will
generate a `Vinograd\IO\Exception\IOException` exception, followed by possible data loss. It is recommended that you
check
that all files are present in the object model before performing the move.



> 🔋 Example of using the [move()](moving-directories.md) method.

### 👉 delete

The `delete()` method removes an object model from the system by physically deleting all associated files and folders.

The deletion process is very simple: after creating the object model, the `delete()` method is called on the root
directory.

Calling this method causes all references to other objects in the model to become invalid, and file and directory
objects become unusable in the program. Physically associated files and directories are removed from the disk.

It is important to note that if you want to delete a directory with all its contents, you need to add all the files and
subdirectories contained in that directory to the model. If any elements are left out, this may throw a
`Vinograd\IO\Exception\IOException` and result in the directory's contents being partially deleted.


> 🔋 Example of using the [delete()](removing-directories.md) method.

## Application concept for examples

Imagine that we are creating an application consisting of modules, each of which has a clear structure with fixed
directory names for storing configurations, controllers, models and views. Our task is to automate the development
process by writing a number of console commands that help manage the structure of application modules. We will not write
the modules themselves; in the examples we will manage the directories and files of these modules.

### Directory Structure

The fictitious application has the following structure:

```
example/
├── private/
│   └── packages/
│       ├── Vendor1/
│       │   ├── ModuleName1/
│       │   │   ├── composer.json
│       │   │   ├── etc/
│       │   │   │   └── package.xml
│       │   │   ├── Controller/
│       │   │   ├── Model/
│       │   │   ├── View/
│       │   │   └── Test/
│       │   └── ... Other modules with the same structure
│       │
│       └──Vendor2/
│          └── ... same structure as in Vendor1
├── public/
└── var/
    └── cache/
```

The path `example/private/packages/` contains the modules to be created. The `Vendor1/` folder is the name of the module
vendor, and `ModuleName1/` is the name of the module. The provider and module names are specified in the namespaces of
each generated class in the module. The controller namespace in a module might look like
this: `namespace Vendor1\ModuleName1\Controller;`.

The `etc` directory is mandatory and is present in each module; other directories located in the module must be named as
indicated in the structure, but they may not be in the module if there is no need for them. The `Test` directory
contains tests for the module.

The `example/public/` folder is the entry point to the application; js files, css and images can be stored in it.
Folder `example/var/cache/` - application cache.

### Contents of files from the structure

In the directories you can find two files: `package.xml` and `composer.json`, which are an important part of each module
and must be present. The purpose of the `composer.json` file is clear, but as for the `package.xml` file, it stores
module metadata. It contains two main nodes `name` and `version`. The `name` node stores the name of the module, and
the `version` node stores its version. To make modules portable, a `composer.json` file is created. It specifies the
name of the module and the startup section "psr-4" specifies where the files with the module's namespace can be found.

📄 Contents of `package.xml`:

```xml
<?xml version="1.0"?>
<packege>
    <name>Vendor1/ModuleName1</name>
    <version>1.0.0</version>
</packege>
```

📄 Contents of `composer.json`:

```json
{
  "name": ":vendor/:module_name",
  "require": {
    "php": ">=8.0"
  },
  "autoload": {
    "psr-4": {
      "Vendor1\\ModuleName1\\": ""
    }
  }
}
```

[Table of contents](../../README.md#user-guide)