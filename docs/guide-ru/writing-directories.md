# Запись директорий

В этом разделе рассматривается пример процесса записи файлов и директорий. Процесс включает два основных этапа:

1) Создание объектной модели дерева файлов и директорий.
2) Запись созданного дерева на диск.

## Пример

В этом примере используются структурные методы для работы с объектами директорий. Если вы ещё не читали этот раздел, я
рекомендую вам прочитать. Ссылка на раздел ["Структурные методы директорий"](directory-structural-methods.md).

> 📌 Консольная команда в этом примере создается для выдуманного приложения. Для понимания, как работает метод записи и
> понимания структуры модуля в примере, следует прочитать раздел с [описанием](applied-methods.md). Код для запуска
> можно скачать из этого [репозитория](https://github.com/vinogradsoft/example).

### Постановка задачи

Для частичной автоматизации создания unit тестов требуется разработать консольную команду. Команда должна принимать имя
класса в качестве параметра и создавать тестовый класс с пустыми методами для каждого публичного метода исходного
класса. Имя тестового класса должно иметь суффикс `Test`. В теле класса unit теста должен быть импортирован тестируемый
класс оператором `use`. Пространство имен тестового класса должно быть сформировано с учетом его расположения.
Тестирующий класс должен быть помещен в директорию `<Vendor Name>/<Module Name>/Test/Unit/<sub directories>`,
где `<sub directories>` - это директории из пространства имен, которые идут после директории `<Module Name>`. Если
тестируемый класс называется `\VendorName\ModuleName\Model\Model` и находится в директории:

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

Тогда файла unit теста `ModelTest.php` должен иметь такое расположение:

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

### Реализация

Для создания тестового класса нам понадобится шаблон, который мы будем преобразовывать в класс unit теста. Назовем
его  `test.php.tpl`.

Содержимое файла `test.php.tpl`:

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

Как вы можете заметить, в шаблоне расставлены метки ":namespace", ":use", ":class_name" и ":methods". Эти метки мы будем
заменять на данные тестируемого класса функцией `str_replace`.

Напишем класс `TestCreator`, который создаст модель файловой системы для тестового класса и запишет ее на диск.

Код:

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

Это класс содержит два метода: `create` и `fillOutTemplate`.
В методе `fillOutTemplate` создается содержимое для класса unit теста на основе шаблона.

Метод `create` создает модель файловой системы будущего тестового класса, затем записывает ее на диск.
Рассмотрим более детально метод `create`. С начала в нем формируются директории для тестов на основе полного названия
переданного класса в аргументе `$className`. Затем в строках

```php
$testTemplateFile = File::createBinded(__DIR__ . '/test.php.tpl');

$testTemplateFile->read();
$content = $testTemplateFile->getContent();
```

создается связанный с файловой системой объект файла шаблона и с помощью метода `read()` считывается его содержимое,
которое присваивается переменной `$content`. После этих строк в коде содержимое шаблона преобразуется
методом `fillOutTemplate`. В объекте файла шаблона заменяется содержимое на преобразованное. Именно это содержимое будет
записано в файл тестового класса. Далее методом `setLocalName` меняется имя объекта файла шаблона на имя тестового
класса. В строке `$lastDirectory->addFile($testTemplateFile);` добавляется в последний сформированный объект директории
объект файла шаблона с новым содержимым. И последним этапом вся созданная модель записывается на диск вызовом
метода `writeTo` у корневой директории.

Наконец, создадим файл, который будем запускать из консоли. Файл будет называться `writeTo`.

Код файла `writeTo`:

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

В этом файле выполняется следующая последовательность действий: сначала считывается имя класса из переданного параметра,
затем проверяется наличие этого класса и происходит его подключение. После этого вызывается метод `create` объекта
`TestCreator` с указанием аргументов - пути к модулям и имени класса, для которого необходимо создать unit тест.

В итоге команда может выглядеть так:

```
php writeTo -c=<Сlass Name>'
```

Пример для запуска вы можете клонировать себе из этого [репозитория](https://github.com/vinogradsoft/example).

Чтобы запустить команду, перейдите в консоли в папку с проектом:

```
cd path/to/cloned/example/
```

Где `path/to/cloned` та папка, в которую вы скачали пример.
Создавать тестовые классы можно командой `php bin/writeTo -c=<Сlass Name>'`.
Для быстрого старта в проекте примера есть существующий класс `\VendorName\ModuleName\Model\Model` в папке модулей, над
которым можно поэкспериментировать. Команда для создания unit теста для этого класса выглядит так:

```
php bin/writeTo -c="\VendorName\ModuleName\Model\Model"
```

После выполнения этой команды будет создан класс теста по
пути `example/private/packages/VendorName/ModuleName/Test/Unit/Model/ModelTest.php` со следующим содержимым:

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

Вы можете создать свои классы в папке модулей с различной вложенностью директорий и поэкспериментировать над ними.

[К оглавлению](../../README_ru_RU.md#руководство)