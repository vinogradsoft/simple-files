# Чтение файлов

В этом разделе мы познакомимся с процессом чтения данных из файлов. Рассмотрим метод чтения и изучим, как
обрабатывать ошибки, возникающие при чтении данных из файлов.

## Получение данных

Чтобы получить данные из файла, нужно сначала выполнить метод `read()`. Этот метод ничего не возвращает
он просто, читает их из файла. За тем можно их получить методом `getContent()`;

Пример получения данных из файла:

```php
<?php
require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

use Vinograd\SimpleFiles\File;

$file = File::createBinded('path/to/file.txt');
$file->read();
echo $file->getContent(); 
```

Пример получения данных из файла в случае с отложенным связыванием с файловой системой:

```php
<?php
require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

use Vinograd\SimpleFiles\File;

$file = new File('file.txt');
$file->bindWithFilesystem('path/to');
$file->read();
echo $file->getContent(); 
```

## Обработка ошибок

До вызова метода `read()` нужно, чтобы объект был связан с файловой системой. Если вызвать без связи, будет выброшено
исключение `\LogicException`. Проверить есть ли связь, можно методом `isBinded()`, метод вернет `true`,
если связь есть, если связи нет - `false`.

Пример в котором будет выброшено исключение `\LogicException`:

```php
$file = new File('file.txt');
try{
    $file->read();
}catch (\LogicException $e){
    echo $e->getMessage();
}
```

Если в логике программы работы с файлами происходит удаление связанного файла (не методами библиотеки) в промежутке
между вызовом метода `bindWithFilesystem(...)` и метода `read()` – может возникнуть исключение
`\Vinograd\IO\Exception\NotFoundException`.

Пример в результате которого будет выброшено исключение `\Vinograd\IO\Exception\NotFoundException`:

```php
$file = new File('file.txt');
$file->bindWithFilesystem('path/to');
\unlink('path/to/file.txt');
try{
    $file->read();
}catch (\Vinograd\IO\Exception\NotFoundException $e){
    echo $e->getMessage();
}
```

[К оглавлению](../../README_ru_RU.md#руководство)