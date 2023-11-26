# Копирование директорий

В этом разделе рассматривается пример процесса копирования файлов и директорий. Процесс включает два основных этапа:

1) Создание объектной модели дерева файлов и директорий.
2) Копирование созданного дерева на диск.

## Пример копирования

В этом примере будет использоваться слушатель `FileBeforeWriteListener` и структурные методы для работы с директориями.
Если вы ещё не читали разделы, в которых описываются эти элементы, я рекомендую вам прочитать их. Ссылки на
разделы ["Обработка содержимого файла перед записью"](processing-file-contents-before-writing.md)
и ["Структурные методы директорий"](directory-structural-methods.md).

> Консольная команда в этом примере создается для выдуманного приложения. Для понимания, как работает метод копирования
> и понимания структуры модуля в примере, следует прочитать раздел с [описанием](applied-methods.md). Код для запуска
> можно скачать из этого [репозитория](https://github.com/vinogradsoft/example).

### Постановка задачи

Нам предстоит разработать консольную команду, которая позволит создавать новый модуль по заданному
шаблону, избегая необходимости создавать структуру для новых модулей вручную.

> Исключительно для примера сделаем один файл шаблона для `composer.json` с названием `composer.json.tpl`, а
> второй шаблон не будем хранить на диске, таким образом изобразим некое вычисляемое содержимое файла `package.xml`,
> чтобы раскрыть в примере смешанную объектную модель в которой есть файлы существующие на диске, и несуществующие.

### Реализация

Шаблон для `package.xml` будет иметь такой вид:

```xml
<?xml version="1.0"?>
<packege>
    <name>:vendor/:module_name</name>
    <version>1.0.0</version>
</packege>
```

Для удобства замены содержимого узла `<name>` мы оставим метки - `:vendor` и `:module_name`. Именно эти метки
будем заменять обычной функцией `str_replace`.

В файле шаблона `composer.json.tpl` сделаем то же самое, только расставим метки в двух местах: в секциях "name" и
"psr-4". Для секции "psr-4" метки будут иметь первую букву заглавной, чтобы не спутать секции при замене.

Создадим файл шаблона `composer.json.tpl` и поместим в него такое содержимое:

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

Приступим к написанию кода. Создадим слушатель `TemplateHandler` для модификации наших заготовленных шаблонов согласно
переданных в консоль параметров.

Код:

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

В этом слушателе для упрощения примера, мы сделали конструктор через который будем передавать названия вендора и модуля.

Из представленного кода видно, что метод `beforeWrite` завершается, если выполняемое действие не является копированием.
Если же выполняется копирование, получаем содержимое файла и в зависимости от обрабатываемого файла
производим соответствующую замену в шаблоне. Затем измененное содержимое устанавливаем обратно в объект файла методом
`setContent`.

Напишем класс `ModuleCreator` который будет создавать модель файловой системы модуля и копировать ее в указанную папку.

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

Класс состоит из одного метода `create`. Первым параметром мы будем принимать путь - где создавать структуру модуля.
Вторым параметром название вендора. И третьим название модуля.

В теле метода сначала мы создаем обязательные объекты директорий `etc`,`Controller`,`Model` и `View`. Затем создаем два
объекта директорий с именами вендора и модуля взятые из аргументов. Далее создаем два объекта файла, один из
которых создается связанным с шаблоном `composer.json.tpl` на диске. Потом устанавливаем шаблон для файла `package.xml`
в виде строки, используя метод `setContent`. Файлу `composer.json.tpl` меняем название на целевое `composer.json`, и
обоим объектам файла добавляем слушатель `TemplateHandler`. Последним этапом формируем объектную модель файлов и
директорий модуля, такую которая должна быть в нашем приложении. Затем выполняем метод `copy` передав путь куда
копировать модель.

Когда вызовется метод `copy`, произойдет такая работа: сначала создадутся все папки начиная с корневой, затем файлы,
причем связанный файл будет сначала загружен и перед записью на диск модифицирован.

Наконец создадим файл, который будем запускать из консоли, так называемую, точку входа в приложение. Файл будет
называться `copy`.

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

В файле `copy` мы принимаем параметры названий вендора и модуля: `-v` и `-m`. Где `-v` это название вендора, а `-m`
название модуля. Потом полученные параметры передаем в метод `create` нашего класса `ModuleCreator` в месте с путем к
папке где будем создавать модули (`__DIR__ . '/../private/packages'`).

В итоге команда может выглядеть так:

```
php copy -v=VendorName -m=ModuleName
```

Пример для запуска вы можете клонировать себе из этого [репозитория](https://github.com/vinogradsoft/example).

Чтобы запустить команду, перейдите в консоли в папку `bin` скачанного проекта:

```
cd path/to/cloned/example/bin/
```

Где `path/to/cloned` та папка, в которую вы скачали пример.   
После этого можно создавать новые модули командой `php copy -v=<Vendor Name> -m=<Module Name>`.
Повторный вызов команды с теми же параметрами приведет к перезаписи файлов `package.xml` и `composer.json` в модуле.

[К оглавлению](../../README.md#руководство)