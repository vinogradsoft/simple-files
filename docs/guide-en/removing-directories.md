# Removing Directories

This section provides an example of the process of deleting files and directories. The process includes two main stages:

1) Creating an object model of a tree of files and directories associated with the file system.
2) Deleting files from disk based on the created model.

## Example

This example uses structured methods to work with directory objects. If you haven't read this section yet, I recommend
you do. Link to the section ["Structural directory methods"](directory-structural-methods.md).

> 📌 The console command in this example is created for a fictitious application. To understand how the removal method
> works and understand the structure of the module in the example, you should read the [description](applied-methods.md)
> section. The launch code can be downloaded from this [repository](https://github.com/vinogradsoft/example).

### Formulation of the problem

To clear the cache you need to run a console command. The command must have no parameters and delete
the `example/var/cache` folder and its contents.

> ❗ This is a conceptual example. For simplicity, in the example we assume that the `example/var/cache` folder can only
> contain files. If there are subdirectories in this folder, this example will not work. To be able to delete subfolders
> with files, you need to traverse the directory recursively and assemble the object model exactly as on the disk. To
> understand the removal process, this example is sufficient.

### Implementation

Let's write a class that will delete the `example/var/cache` folder.

```php
<?php

namespace Example;

use Vinograd\SimpleFiles\Directory;
use Vinograd\SimpleFiles\File;

class CacheCleaner
{

    public function clean(string $path): void
    {
        $cacheFiles = glob($path . '/*');
        $varCacheDirectory = Directory::createBinded($path);
        foreach ($cacheFiles as $filePath) {
            $varCacheDirectory->addFile(File::createBinded($filePath));
        }
        $varCacheDirectory->delete();
    }

}
```

The class consists of one `clean` method, which takes as a parameter the absolute path to the `example/var/cache`
folder.

The method body code consists of three main parts: getting all the paths to the files in a given folder, creating a file
system model based on these paths, and finally deleting the `example/var/cache` folder and all the files it contains
from the disk using the resulting model.

Let's look at the process. The `glob` function finds all files based on the `$path` argument. Then a directory object is
created associated with the folder along the path taken from the value of the `$path` argument (`.../example/var/cache`)
on disk. Next, in the loop, file objects associated with real files on the disk are created and added to the directory
object. When the entire model is created, the `delete` method is called on the directory object.

> ❗ An important feature of the “delete” method is that after calling it, not only files and directories are deleted
> from the disk, but also all internal connections of the model being deleted are destroyed. Such a model becomes
> unsuitable for subsequent use in code.

Let's write a “launch file” in which we will check whether the `example/var/cache` folder exists. If it exists, call the
`clean` method on the created `\Example\CacheCleaner` object, passing it the path to the cache folder.

Code:

```php
#!/usr/bin/env php
<?php
declare(strict_types=1);

require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

if (PHP_SAPI !== 'cli') exit(1);

$varPath = realpath(__DIR__ . '/../var');
$varCachePath = $varPath . '/cache';

$message = 'The cache has been cleared.' . PHP_EOL;

if (!is_dir($varCachePath)) {
    echo $message;
    exit(0);
}

(new \Example\CacheCleaner())->clean($varCachePath);
echo $message;
```

As a result, the command to clear the cache will be like this:

```
php delete
```

You can clone an example to run from this [repository](https://github.com/vinogradsoft/example).

To run the command, go to the `bin` folder of the downloaded project in the console:

```
cd path/to/cloned/example/bin/
```

Where `path/to/cloned` is the folder where you downloaded the example. After this, you can clear the cache with
the `php delete` command.

In the example project, a folder `example/var/cache` has already been created, which contains three files that simulate
a cache. After executing the command, these three files will be deleted in the location with the `example/var/cache`
directory.

[Table of contents](../../README.md#user-guide)