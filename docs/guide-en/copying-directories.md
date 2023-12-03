# Copying Directories

This section provides an example of the process of copying files and directories. The process includes two main stages:

1) Creating an object model of a tree of files and directories.
2) Copying the created tree to disk.

## Copy example

This example will use the `FileBeforeWriteListener` listener and structured methods to work with directories. If you
have not yet read the sections that describe these elements, I encourage you to read them. Links to
sections ["Processing file contents before writing"](processing-file-contents-before-writing.md)
and ["Structural directory methods"](directory-structural-methods.md).

> 📌 The console command in this example is created for a fictitious application. To understand how the copy method works
> and understand the structure of the module in the example, you should read the [description](applied-methods.md)
> section. The launch code can be downloaded from this [repository](https://github.com/vinogradsoft/example).

### Formulation Of The Problem

We will have to develop a console command that will allow us to create a new module using a given template, avoiding the
need to create a structure for new modules manually.

> ❗ Purely for the sake of example, we will make one template file for `composer.json` called `composer.json.tpl`, and
> we will not store the second template on disk, thus we will display some calculated contents of the `package.xml` file
> in order to reveal in the example a mixed object model in which there are files existing on disk, and non-existent.

### Implementation

The template for `package.xml` will look like this:

```xml
<?xml version="1.0"?>
<packege>
    <name>:vendor/:module_name</name>
    <version>1.0.0</version>
</packege>
```

For the convenience of replacing the contents of the `<name>` node, we will leave the tags - `:vendor`
and `:module_name`. It is these labels that we will replace with the usual `str_replace` function.

In the template file `composer.json.tpl` we will do the same thing, only we will place the labels in two places: in the
“name” and “psr-4” sections. For the "psr-4" section, the labels will have the first letter capitalized so as not to
confuse the sections when replacing.

Let's create a template file `composer.json.tpl` and place the following content in it:

```json
{
  "name": ":vendor/:module_name",
  "require": {
    "php": ">=8.0"
  },
  "autoload": {
    "psr-4": {
      ":Vendor\\:Module_name\\": ""
    }
  }
}
```

Let's start writing code. Let's create a `TemplateHandler` listener to modify our prepared templates according to the
parameters passed to the console.

Code:

```php
<?php
declare(strict_types=1);

namespace Example;

use Vinograd\SimpleFiles\File;
use Vinograd\SimpleFiles\Event\FileBeforeWriteListener;

class TemplateHandler implements FileBeforeWriteListener
{

    private string $vendor;
    private string $moduleName;

    /**
     * @param string $vendor
     * @param string $moduleName
     */
    public function __construct(string $vendor, string $moduleName)
    {
        $this->vendor = $vendor;
        $this->moduleName = $moduleName;
    }

    /**
     * @inheritDoc
     */
    public function beforeWrite(File $file, string $keyOperation): void
    {
        if ($keyOperation !== File::COPY) {
            return;
        }
        $content = $file->getContent();

        if ($file->getLocalName() === 'package.xml') {

            $content = str_replace(":vendor", $this->vendor, $content);
            $file->setContent(str_replace(":module_name", $this->moduleName, $content));

        } elseif ($file->getLocalName() === 'composer.json') {

            $content = str_replace(":vendor", strtolower($this->vendor), $content);
            $content = str_replace(":module_name", strtolower($this->moduleName), $content);
            $content = str_replace(":Vendor", $this->vendor, $content);
            $file->setContent(str_replace(":Module_name", $this->moduleName, $content));
        }
    }

}
```

In this listener, to simplify the example, we made a constructor through which we will pass the names of the vendor and
module.

From the code above, you can see that the `beforeWrite` method fails if the action being performed is not a copy. If
copying is performed, we obtain the contents of the file and, depending on the file being processed, make the
appropriate replacement in the template. Then we set the changed content back to the file object using the `setContent`
method.

Let's write a `ModuleCreator` class that will create a module file system model and copy it to the specified folder.

```php
<?php
declare(strict_types=1);

namespace Example;

use Vinograd\SimpleFiles\Directory;
use Vinograd\SimpleFiles\File;

class ModuleCreator
{
    /**
     * @param string $destination
     * @param string $vendor
     * @param string $moduleName
     * @return void
     */
    public function create(string $destination, string $vendor, string $moduleName): void
    {
        $etcDirectory = new Directory('etc');
        $controllerDirectory = new Directory('Controller');
        $modelDirectory = new Directory('Model');
        $viewDirectory = new Directory('View');

        $vendorDirectory = new Directory($vendor);
        $moduleDirectory = new Directory($moduleName);

        $packageXmlFile = new File('package.xml');
        $composerJsonFile = File::createBinded(__DIR__ . '/composer.json.tpl');

        $packageXmlFile->setContent(
            '<?xml version="1.0"?>' . PHP_EOL .
            '<package>' . PHP_EOL .
            '    <name>:vendor/:module_name</name>' . PHP_EOL .
            '    <version>1.0.0</version>' . PHP_EOL .
            '</package>'
        );

        $composerJsonFile->setLocalName('composer.json');

        $templateHandler = new TemplateHandler($vendor, $moduleName);
        $packageXmlFile->addFileBeforeWriteListener($templateHandler);
        $composerJsonFile->addFileBeforeWriteListener($templateHandler);


        $vendorDirectory->addDirectory($moduleDirectory);
        $moduleDirectory->addDirectory($etcDirectory);

        $moduleDirectory->addDirectory($controllerDirectory);
        $moduleDirectory->addDirectory($modelDirectory);
        $moduleDirectory->addDirectory($viewDirectory);

        $etcDirectory->addFile($packageXmlFile);
        $moduleDirectory->addFile($composerJsonFile);

        $vendorDirectory->copy($destination);
    }
}
```

The class consists of one `create` method. The first parameter we will take is the path - where to create the module
structure. The second parameter is the name of the vendor. And the third is the name of the module.

In the body of the method, we first create the required directory objects `etc`,`Controller`,`Model` and `View`. Then we
create two directory objects with the vendor and module names taken from the arguments. Next, we create two file
objects, one of which is created associated with the `composer.json.tpl` template on disk. Then we set the template for
the `package.xml` file as a string using the `setContent` method. Change the name of the `composer.json.tpl` file to
target `composer.json`, and add a `TemplateHandler` listener to both file objects. The last step is to form an object
model of the module’s files and directories, the kind that should be in our application. Then we execute the `copy`
method, passing the path where to copy the model.

When the `copy` method is called, the following work will happen: first, all folders will be created, starting with the
root one, then the files, and the associated file will be first downloaded and modified before being written to disk.

Finally, we will create a file that we will launch from the console, the so-called entry point into the application. The
file will be called `copy`.

```php
#!/usr/bin/env php
<?php
declare(strict_types=1);

use Example\ModuleCreator;

require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

if (PHP_SAPI !== 'cli') exit(1);

$options = getopt('v:m:');

if (empty($options['v']) || empty($options['m'])) {
    echo 'Accept option: -v=<vendor> -m=<module name>';
    exit(1);
}

(new ModuleCreator())->create(
    realpath(__DIR__ . '/../private/packages'),
    $options['v'],
    $options['m']
);
```

In the `copy` file we accept the vendor and module name parameters: `-v` and `-m`. Where `-v` is the vendor name,
and `-m` is the module name. Then we pass the received parameters to the create method of our `ModuleCreator` class in a
place with the path to the folder where we will create modules (`__DIR__ . '/../private/packages'`).

As a result, the command might look like this:

```
php copy -v=VendorName -m=ModuleName
```

You can clone an example to run from this [repository](https://github.com/vinogradsoft/example).

To run the command, go to the `bin` folder of the downloaded project in the console:

```
cd path/to/cloned/example/bin/
```

Where `path/to/cloned` is the folder where you downloaded the example.
After this, you can create new modules with the command `php copy -v=<Vendor Name> -m=<Module Name>`.
Calling the command again with the same parameters will overwrite the `package.xml` and `composer.json` files in the
module.

[Table of contents](../../README.md#user-guide)