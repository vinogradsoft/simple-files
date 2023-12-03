# Processing File Contents Before Writing

Before writing a file to disk, you can perform some actions on its contents. To do this, use the
`Vinograd\SimpleFiles\Event\FileBeforeWriteListener` event listener. It allows you to modify data before storing it in
the file system.

> 📢 `Vinograd\SimpleFiles\Event\FileBeforeWriteListener` is an interface that allows you to add some logic for
> processing the contents of a file before writing it to the file system. This interface has a `beforeWrite` method that
> is called every time before writing a file.

You can register listeners for a file writing event using the `addFileBeforeWriteListener` method of an object of the
`Vinograd\SimpleFiles\File` class. This method does not return any values. You need to pass an argument that implements
the `Vinograd\SimpleFiles\Event\FileBeforeWriteListener` interface.

There is no way to control the order in which added listeners are fired other than registering them in the desired
order.

## beforeWrite Method Overview

The first argument to the `beforeWrite` method is the file object that is being written. The second is the so-called
operation key, which identifies the recording execution context. It can have several values: "WRITE", "WRITE_TO", "COPY"
and "MOVE". Using these keys, you can distinguish between file writing execution contexts.

Example of implementation and adding a listener:

```php
<?php
require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

use Vinograd\SimpleFiles\Event\FileBeforeWriteListener;
use Vinograd\SimpleFiles\File;

$file = File::createBinded('path/to/file.txt');
$file->read();
$file->addFileBeforeWriteListener(
    new class() implements FileBeforeWriteListener {
        public function beforeWrite(File $file, string $keyOperation): void
        {
            if ($keyOperation === File::WRITE_TO) {
                $file->setContent('modified ' . $file->getContent());
            }
        }
    }
);
$file->setLocalName('newFile.txt');
$file->writeTo('path/to/new/directory');
```

To simplify the example, we use an anonymous class that implements the
`Vinograd\SimpleFiles\Event\FileBeforeWriteListener` interface. The `beforeWrite` method modifies the data being written
only if the `writeTo` method of the `Vinograd\SimpleFiles\File` object is used.

[Table of contents](../../README.md#user-guide)