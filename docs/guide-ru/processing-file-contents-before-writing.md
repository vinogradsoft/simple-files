# Обработка содержимого файла перед записью

Перед записью файла на диск можно выполнить некоторые действия с его содержимым. Для этого используется слушатель
события `Vinograd\SimpleFiles\Event\FileBeforeWriteListener`. Он позволяет модифицировать данные перед их сохранением в
файловой системе.

> `Vinograd\SimpleFiles\Event\FileBeforeWriteListener` - это интерфейс, который позволяет добавлять некую логику
> обработки содержимого файла до записи его в файловую систему. В этом интерфейсе есть метод `beforeWrite` который
> вызывается всякий раз перед записью файла.

Зарегистрировать слушателей на событие записи файла можно с помощью метода `addFileBeforeWriteListener` объекта класса
`Vinograd\SimpleFiles\File`. Этот метод не возвращает никаких значений. Вам нужно передать аргумент,
который реализует интерфейс `Vinograd\SimpleFiles\Event\FileBeforeWriteListener`.

Управлять порядком запуска добавленных слушателей нет никакого способа, кроме как зарегистрировать их в нужной
последовательности.

## Обзор метода beforeWrite

В метод `beforeWrite` первым аргументом передается объекта файла который записывается. Вторым, так называемый, ключ
операции который идентифицирует контекст выполнения записи. Он может иметь несколько значений: "WRITE", "WRITE_TO",
"COPY" и "MOVE". С помощью этих ключей можно различать контексты выполнения записи файла.

Пример реализации и добавления слушателя:

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

Для простоты примера используется анонимный класс, который реализует
интерфейс `Vinograd\SimpleFiles\Event\FileBeforeWriteListener`. В методе `beforeWrite` выполняется модификация
записываемых данных только в том случае, если используется метод `writeTo` объекта `Vinograd\SimpleFiles\File`.

[К оглавлению](../../README.md#руководство)