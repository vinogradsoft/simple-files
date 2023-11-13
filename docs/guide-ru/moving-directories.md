# Перемещение директорий

В этом разделе рассматривается пример процесса перемещения файлов и директорий. Процесс включает два основных этапа:

1) Создание объектной модели дерева файлов и директорий.
2) Перемещение созданного дерева на диск.

## Пример

В этом примере используются структурные методы для работы с объектами директорий. Если вы ещё не читали этот раздел, я
рекомендую вам прочитать. Ссылка на раздел ["Структурные методы директорий"](directory-structural-methods.md).

> Консольная команда в этом примере создается для выдуманного приложения. Для понимания как работает метод перемещения
> и понимания структуры модуля в примере, следует прочитать раздел с [описанием](applied-methods.md). Код для запуска
> можно скачать из этого [репозитория](https://github.com/vinogradsoft/example).

### Постановка задачи

Для автоматизации перемещения картинок требуется создать консольную команду, которая по не коему шаблону сможет найти
картинки в модуле и переместить их в папку `public/images`. Консольная команда должна принимать два аргумента: название
вендора и модуля в формате `<Vendor Name>/<Module Name>` и шаблон поиска в виде строки, такой же как принимает
функция php [glob](https://www.php.net/manual/ru/function.glob.php).

### Реализация

Напишем класс, который будет перемещать изображения в папку `example/public/images`.

Код:

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

Класс имеет один метод `move` с двумя аргументами: `$destination` и `$pattern`. Первый `$destination` - место назначения
для изображений (абсолютный путь до папки `example/public`). Второй `$pattern` - шаблон для поиска изображений.

Код довольно прост: функция `glob` сначала ищет изображения по указанному шаблону. После этого создается объект
директории назначения - `$imagesDirectory`. Затем в цикле создаются изображения, найденные по указанному шаблону, и
добавляются в объект директории `$imagesDirectory`. В конце вызывается метод `move` у объекта `$imagesDirectory` с
заданным путем назначения. В результате создается директория `images` в `example/public` (если она еще не существует) и
в неё перемещаются файлы изображений из текущего модуля.

Напишем файл запуска из консоли:

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

В этом файле создаётся объект `Example\ImageMover`, вызывается его метод `move`, передавая подготовленные параметры.
Важно отметить, что к паттерну добавляется путь к папке `View` целевого модуля для поиска изображений именно в ней.

В итоге команда может выглядеть так:

```
php move -v="VendorName/ModuleName" -p="*.png"
```

Пример для запуска вы можете клонировать себе из этого [репозитория](https://github.com/vinogradsoft/example).

Чтобы запустить команду, перейдите в консоли в папку с проектом:

```
cd path/to/cloned/example/bin/
```

Где `path/to/cloned` та папка, в которую вы скачали пример.
После этого можно перемещать изображения из модулей.

Команда ниже переместит все изображения с расширением `png` в модуле `VendorName/ModuleName` в
папку `example/public/images`. Причем если папка `images` не существует, она будет создана.

```
php move -v="VendorName/ModuleName" -p="*.png"
```

[К оглавлению](../../README.md#руководство)