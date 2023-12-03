# Writing Directories

This section provides an example of the process of writing files and directories. The process includes two main stages:

1) Creating an object model of a tree of files and directories.
2) Write the created tree to disk.

## Example

This example uses structured methods to work with directory objects. If you haven't read this section yet, I recommend
you do. Link to the section ["Structural directory methods"](directory-structural-methods.md).

> 📌 The console command in this example is created for a fictitious application. To understand how the recording method
> works and understand the structure of the module in the example, you should read the [description](applied-methods.md)
> section. The launch code can be downloaded from this [repository](https://github.com/vinogradsoft/example).

### Formulation of the problem

To partially automate the creation of unit tests, you need to develop a console command. The command should take the
class name as a parameter and create a test class with empty methods for each public method of the original class. The
test class name must have the `Test` suffix. In the body of the test unit class, the class under test must be imported
with the `use` statement. The namespace of the test class must be formed taking into account its location. The testing
class should be placed in the `<Vendor Name>/<Module Name>/Test/Unit/<sub directories>` directory,
where `<sub directories>` are the namespace directories that come after the `<Module Name>` directory. If the class
under test is called `\VendorName\ModuleName\Model\Model` and is located in the directory:

```
example/
|
|____private/
     |
     |____packages/
          |
          |____Vendor/
               |
               |____ModuleName/
                    |
                    |____Model/
                         |
                         |____Model.php 
```

Then the unit test file `ModelTest.php` should have the following location:

```
example/
|
|____private/
     |
     |____packages/
          |
          |____Vendor/
               |
               |____ModuleName/
                    |
                    |____Test/
                         |
                         |____Unit/
                              |
                              |____Model/
                                   |
                                   |____ModelTest.php
```

### Implementation

To create a test class, we need a template, which we will convert into a unit test class. Let's call it `test.php.tpl`.

Contents of the file `test.php.tpl`:

```php
<?php
declare(strict_types=1);

namespace :namespace;

use PHPUnit\Framework\TestCase;
use :use;

class :class_nameTest extends TestCase
{

:methods
}
```

As you can see, the template contains the labels ":namespace", ":use", ":class_name" and ":methods". We will replace
these labels with the data of the tested class using the `str_replace` function.

Let's write a `TestCreator` class that will create a file system model for the test class and write it to disk.

Code:

```php
<?php
declare(strict_types=1);

namespace Example;

use Vinograd\SimpleFiles\Directory;
use Vinograd\SimpleFiles\File;

class TestCreator
{
    public function create(string $destination, string $className): void
    {
        $classNameArray = explode('\\', $className);

        $shortClassName = array_pop($classNameArray); // extract class short name

        $vendorDirectory = new Directory(array_shift($classNameArray)); // Vendor
        $lastDirectory = $vendorDirectory->addDirectory(new Directory(array_shift($classNameArray))); // Module
        $lastDirectory = $lastDirectory->addDirectory(new Directory('Test'));
        $lastDirectory = $lastDirectory->addDirectory(new Directory('Unit'));
        $lastDirectory = $lastDirectory->addDirectory(new Directory(array_shift($classNameArray))); // Required directory

        foreach ($classNameArray as $directoryName) {
            $lastDirectory = $lastDirectory->addDirectory(new Directory($directoryName)); //other subdirectories
        }

        $testTemplateFile = File::createBinded(__DIR__ . '/test.php.tpl');

        $testTemplateFile->read();
        $content = $testTemplateFile->getContent();

        $content = $this->fillOutTemplate($className, $lastDirectory, $content, $shortClassName);

        $testTemplateFile->setContent($content);
        $testTemplateFile->setLocalName($shortClassName . 'Test.php');

        $lastDirectory->addFile($testTemplateFile);

        $vendorDirectory->writeTo($destination);
    }

    private function fillOutTemplate(string $className, Directory $directory,
                                     string $content, string $shortClassName): string
    {
        $class = new \ReflectionClass($className);
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        $testMethods = [];
        foreach ($methods as $method) {
            $testMethods [] = '    public function test' . ucfirst($method->getName()) . '() {}' . PHP_EOL;
        }

        $content = str_replace(
            ':namespace',
            str_replace('/', '\\', $directory->getLocalPath()), $content
        );

        $content = str_replace(':use', $className, $content);
        $content = str_replace(':class_name', $shortClassName, $content);
        return str_replace(':methods', implode(PHP_EOL, $testMethods), $content);
    }
}
```

This class contains two methods: `create` and `fillOutTemplate`.
The `fillOutTemplate` method creates content for the test unit class based on the template.

The `create` method creates a file system model of the future test class, then writes it to disk.
Let's take a closer look at the `create` method. From the beginning, it creates directories for tests based on the full
name of the passed class in the `$className` argument. Then in the lines

```php
$testTemplateFile = File::createBinded(__DIR__ . '/test.php.tpl');

$testTemplateFile->read();
$content = $testTemplateFile->getContent();
```

a template file object associated with the file system is created and its contents are read using the `read()` method,
which is assigned to the `$content` variable. After these lines of code, the contents of the template are converted by
the `fillOutTemplate` method. The content in the template file object is replaced with the converted one. It is this
content that will be written to the test class file. Next, the `setLocalName` method changes the name of the template
file object to the name of the test class. In the line `$lastDirectory->addFile($testTemplateFile);` a template file
object with new content is added to the last generated directory object. And the last step is to write the entire
created model to disk by calling the `writeTo` method on the root directory.

Finally, let's create a file that we will run from the console. The file will be called `writeTo`.

File code `writeTo`:

```php
#!/usr/bin/env php
<?php
declare(strict_types=1);

use Example\TestCreator;

require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

if (PHP_SAPI !== 'cli') exit(1);

$options = getopt('c:');

if (empty($options['c'])) {
    echo 'Accept option: -c=<class name>';
    exit(1);
}

$packages = realpath(__DIR__ . '/../private/packages');
$class = ltrim(trim($options['c']), " \t\n\r\0\x0B\\");

$pathToPhpClass = str_replace('\\', '/', $packages . '/' . $class . '.php');
if (!file_exists($pathToPhpClass)) {
    echo 'There is no such class. Check your spelling. Example php writeTo -c="\VendorName\ModuleName\Model\Model"', PHP_EOL;
    exit(1);
}
require_once $pathToPhpClass;

(new TestCreator())->create(
    $packages,
    $class
);
```

In this file, the following sequence of actions is performed: first, the class name is read from the passed parameter,
then the presence of this class is checked and it is connected. After this, the `create` method of the `TestCreator`
object is called, specifying the arguments - the path to the modules and the name of the class for which the unit test
needs to be created.

As a result, the command might look like this:

```
php writeTo -c=<Class Name>'
```

You can clone an example to run from this [repository](https://github.com/vinogradsoft/example).

To run the command, go to the project folder in the console:

```
cd path/to/cloned/example/
```

Where `path/to/cloned` is the folder where you downloaded the example.
You can create test classes with the command `php bin/writeTo -c=<Сlass Name>'`.
To get you started quickly, the example project has an existing class `\VendorName\ModuleName\Model\Model` in the
modules folder that you can experiment with. The command to create a unit test for this class looks like this:

```
php bin/writeTo -c="\VendorName\ModuleName\Model\Model"
```

After executing this command, a test class will be created according to
path `example/private/packages/VendorName/ModuleName/Test/Unit/Model/ModelTest.php` with the following content:

```php
<?php
declare(strict_types=1);

namespace VendorName\ModuleName\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use VendorName\ModuleName\Model\Model;

class ModelTest extends TestCase
{

    public function testGetName() {}

    public function testSetName() {}

}
```

You can create your classes in the modules folder with different nesting of directories and experiment with them.

[Table of contents](../../README.md#user-guide)