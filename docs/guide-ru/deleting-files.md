# Удаление файлов

Для удаления файлов в классе `Vinograd\SimpleFiles\File` есть метод `delete()`. Он направлен исключительно на удаление
файлов расположенных в файловой системе. Этим методом нельзя удалять файлы которые не связаны с файловой системой. Вызов
этого метода у объекта не связанного с файловой системой приведет к выбрасыванию исключения `\LogicException`. Во
избежание таких ситуаций, следует внимательно относиться к созданию ненужных файлов в коде.

Пример удаления файла:

```php
$file = File::createBinded('path/to/file.txt');
$file->delete();
```

В случаях когда объект был успешно связан с файловой системой, но до вызова метода `delete()` был удален сторонней
системой, будет выброшено исключение `\Vinograd\IO\Exception\NotFoundException` как показано в этом пример:

```php
$file = File::createBinded('path/to/file.txt');
\unlink('path/to/file.txt');
try{
    $file->delete();
}catch (\Vinograd\IO\Exception\NotFoundException $exception){
    echo $exception->getMessage();
}
```

[К оглавлению](../../README_ru_RU.md#руководство)