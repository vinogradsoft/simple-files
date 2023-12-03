# Moving Directories

This section provides an example of the process of moving files and directories. The process includes two main stages:

1) Creating an object model of a tree of files and directories.
2) Moving the created tree to disk.

## Example

This example uses structured methods to work with directory objects. If you haven't read this section yet, I recommend
you do. Link to the section ["Structural directory methods"](directory-structural-methods.md).

> 📌 The console command in this example is created for a fictitious application. To understand how the move method works
> and understand the module structure in the example, you should read the [description](applied-methods.md) section. The
> launch code can be downloaded from this [repository](https://github.com/vinogradsoft/example).

### Formulation of the problem

To automate the movement of pictures, you need to create a console command that, using a certain template, can find
pictures in the module and move them to the `public/images` folder. The console command must take two arguments: the
name
of the vendor and module in the format `<Vendor Name>/<Module Name>` and a search pattern in the form of a string, the
same as the php [glob](https://www.php.net/manual/en/function.glob.php) function takes.

### Implementation

Let's write a class that will move images to the `example/public/images` folder.

Code:

```php
<?php

namespace Example;

use Vinograd\SimpleFiles\Directory;
use Vinograd\SimpleFiles\File;

class ImageMover
{

    public function move(string $destination, string $pattern): void
    {
        $images = glob($pattern);
        $imagesDirectory = new Directory('images');
        foreach ($images as $image) {
            $imagesDirectory->addFile(File::createBinded($image));
        }
        $imagesDirectory->move($destination);
    }

}
```

The class has one `move` method with two arguments: `$destination` and `$pattern`. The first `$destination` is the
destination for the images (the absolute path to the `example/public` folder). The second `$pattern` is a pattern for
searching images.

The code is quite simple: the `glob` function first searches for images using the specified pattern. After this, a
destination directory object is created - `$imagesDirectory`. The loop then creates images found using the specified
pattern and adds them to the `$imagesDirectory` directory object. Finally, the move method is called on the
$imagesDirectory object with the given destination path. This creates an `images` directory in `example/public` (if it
does not already exist) and moves the image files from the current module into it.

Let's write a launch file from the console:

```php
#!/usr/bin/env php
<?php
declare(strict_types=1);

require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

if (PHP_SAPI !== 'cli') exit(1);

$options = getopt('v:p:');

if (empty($options['v']) || empty($options['p'])) {
    echo 'Accept option: -v=<Vendor Name/Module Name> -p=<pattern>';
    exit(1);
}

$pathToModules = realpath(__DIR__ . '/../private/packages');
$viewFolder = $pathToModules . '/' . $options['v'] . '/View/';
if (!is_dir($viewFolder)) {
    echo 'View folder not found.', PHP_EOL;
    exit(1);
}

$pattern = $viewFolder . $options['p'];
(new \Example\ImageMover())->move(
    realpath(__DIR__ . '/../public'),
    $pattern
);
```

In this file, an `Example\ImageMover` object is created, its `move` method is called, passing the prepared parameters.
It is important to note that the path to the `View` folder of the target module is added to the pattern to search for
images in it.

As a result, the command might look like this:

```
php move -v="VendorName/ModuleName" -p="*.png"
```

You can clone an example to run from this [repository](https://github.com/vinogradsoft/example).

To run the command, go to the `bin` folder of the downloaded project in the console:

```
cd path/to/cloned/example/bin/
```

Where `path/to/cloned` is the folder where you downloaded the example.
After this, you can move images from modules.

The command below will move all images with a `png` extension in the `VendorName/ModuleName` module to
the `example/public/images` folder. Moreover, if the `images` folder does not exist, it will be created.

```
php move -v="VendorName/ModuleName" -p="*.png"
```

[Table of contents](../../README.md#user-guide)