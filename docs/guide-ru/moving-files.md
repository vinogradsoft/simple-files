# Перемещение файлов

При работе с файлами, одним из ключевых действий является их перемещение. Этот процесс может включать как файлы, уже
существующие в файловой системе, так и те, которые временно хранятся в памяти. Важно помнить, что путь к каталогу, в
который будет перемещен файл, должен существовать. В противном случае будет вызвано соответствующее исключение. Надо
обратить внимание на то, что в отличие от методов `copy` и `writeTo` после перемещения файла меняется путь связи с
источником на тот который получился в результате перемещения.

## Перемещение файлов которые существуют в файловой системе

Чтобы переместить существующий файл, вы можете использовать следующий код:

```php
$file = File::createBinded('path/to/file.txt');
$file->move('path/to/new/directory'); # path/to/new/directory/file.txt
```

Этот код создает экземпляр класса `Vinograd\SimpleFiles\File` с указанием пути к файлу и перемещает его в указанный
каталог с помощью метода `move()`. При этом связь с источником будет уже не `'path/to/file.txt'`,
а `path/to/new/directory/file.txt`.

Если мы хотим переместить файл и изменить его имя в каталоге назначения, мы можем воспользоваться методом `setLocalName`
который изменит имя файла назначения.

Пример кода:

```php
$file = File::createBinded('path/to/file.txt');
$file->setLocalName('fileRenamed.txt');
$file->move('path/to/new/directory'); # path/to/new/directory/fileRenamed.txt
```

## Перемещение файлов которые находятся в памяти

После перемещения файлов которые находятся в памяти устанавливается связь с файловой системой.

Пример:

```php
$file = new File('file.txt');
$file->setContent('content');
$file->move('path/to/new/directory');  # path/to/new/directory/file.txt
```

Перемещение файла с изменением названия файла:

```php
$file = new File('file.txt');
$file->setContent('content');
$file->setLocalName('fileRenamed.txt');
$file->move('path/to/new/directory'); # path/to/new/directory/fileRenamed.txt
```

### Обработка ошибок

Если директория в которую перемещается файл не существует, будет выброшено исключение
`Compass\Exception\InvalidPathException`.

Пример:

```php
$file = File::createBinded('path/to/file.txt');
try{
   $file->move('not/exist/directory/path');
}catch (\Compass\Exception\InvalidPathException $exception){
   echo $exception->getMessage();
}
```

Исключение будет выброшено и для перемещаемых файлов, которые находятся в памяти.

Пример:

```php
$file = new File('file.txt');
$file->setContent('content');
try{
   $file->move('not/exist/directory/path');
}catch (\Compass\Exception\InvalidPathException $exception){
   echo $exception->getMessage();
}
```

[К оглавлению](../../README.md#руководство)