# Копирование файлов

В этом разделе мы рассмотрим метод копирования файлов и обработку ошибок. В использовании метод очень прост, поэтому
проще объяснить как им пользоваться на примерах.

## Примеры

> ❗ Метод копирования не требует чтобы объект был связан с файловой системой. Он позволяет изменять имя файла назначения с
> помощью метода `setLocalName`.

Сделать три копии существующего файла в разных директориях:

```php
$file = File::createBinded('path/to/file.txt');
$file->copy('path/to/directory1'); # path/to/directory1/file.txt
$file->copy('path/to/directory2'); # path/to/directory2/file.txt
$file->copy('path/to/directory3'); # path/to/directory3/file.txt
```

Сделать две копии существующего файла в разных директориях и с разными именами:

```php
$file = File::createBinded('path/to/file.txt');
$file->copy('path/to/directory1'); # path/to/directory1/file.txt
$file->setLocalName('fileRenamed.txt');
$file->copy('path/to/directory2'); # path/to/directory2/fileRenamed.txt
```

Сделать копию файла который не существует в файловой системе:

```php
$file = new File('file.txt');
$file->setContent('content');
$file->copy('path/to/directory1');  # path/to/directory1/file.txt
```

Сделать две копии не существующего файла в разных директориях и с разными именами:

```php
$file = new File('file.txt');
$file->setContent('content');
$file->copy('path/to/directory1'); # path/to/directory1/file.txt
$file->setLocalName('fileRenamed.txt');
$file->copy('path/to/directory2'); # path/to/directory2/fileRenamed.txt
```

## Обработка ошибки

При копировании файлов важно чтобы директория назначения существовала. Следующий пример выбросит исключение
`Compass\Exception\InvalidPathException`

```php
$file = File::createBinded('path/to/file.txt');
try{
   $file->copy('not/exist/directory/path');
}catch (\Compass\Exception\InvalidPathException $e){
   echo $e->getMessage();
}
```

[К оглавлению](../../README_ru_RU.md#руководство)