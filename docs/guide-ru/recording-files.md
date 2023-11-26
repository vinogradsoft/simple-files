# Запись файлов

В этом разделе мы рассмотрим два способа записи данных. Разберем все тонкости работы с записью файлов, чтобы вы могли
уверенно оперировать ими в своих проектах.

В классе `Vinograd\SimpleFiles\File` реализовано два метода записи данных в файловую систему: `write()`
и `writeTo(string $directoryPath)`. Эти методы отличаются тем, что первый использует для записи путь источника с которым
связан, а второй метод записывает в указанную директорию.

## Метод write

> Это метод предназначен для объектов которые установили связь с файловой системой. Если выполните этот метод до
> связывания, будет выброшено исключение `Compass\Exception\InvalidPathException`.

Пример когда файл существует и в него нужно записать новые данные:

```php
$file = File::createBinded('/path/to/file.txt');
$file->setContent('change content');
$file->write();
```

Пример когда файл еще не создан в файловой системе:

```php
$file = new File('file.txt');
$file->bindWithFilesystem('path/to/directory');
$file->setContent('data');
$file->write();
```

При выполнении метода `$file->bindWithFilesystem('path/to/directory');`, если файл существует по
пути `path/to/directory/file.txt`, то система свяжется с ним, если нет, то создаст пустой файл.

> У класса `Vinograd\SimpleFiles\File` есть метод `setLocalName`. Этот метод меняет имя файла. Его работа немного
> специфичная. Если вы создаете файл не связанный с файловой системой посредством оператора `new`, то до связи с
> файловой системой вы можете этим методом изменить его имя. Если вы выполните этот метод после связывания с файловой
> системой, значение которое было установлено этим методом будет использоваться только в методах копирования и
> перемещения файлов, чтобы можно было выполнить эти операции с новыми названиями файлов.

Пример изменения имени файла до связывания его с файловой системой:

```php
$file = new File('file.txt');
$file->setLocalName('fileRenamed.txt');
$file->bindWithFilesystem('path/to/directory');
$file->setContent('data');
$file->write();
```

В этом случае файл будет иметь путь `path/to/directory/fileRenamed.txt`. Если бы мы выполнили метод `setLocalName` после
метода `bindWithFilesystem`, то данные записались бы в файл `path/to/directory/file.txt`. 

## Метод writeTo

> Метод `writeTo` не нуждается в предварительном связывании с файловой системой.<br>
> Директория в которую записывается файл должна существовать, иначе будет выброшено
> исключение `Compass\Exception\InvalidPathException`.

Пример использования до связывания с файловой системой.

```php
$file = new File('file.txt');
$file->setContent('content');
$file->writeTo('path/to/directory');
$file->writeTo('path/to/another/directory');
```

В этом примере данные запишутся в два файла которых не существовало до записи: `path/to/directory/file.txt`
и `path/to/another/directory/file.txt`.

Пример выполнения метода после связывания с файловой системой.

```php
$file = File::createBinded('/path/to/file.txt');
$file->read();
$file->setContent('change '. $file->getContent());
$file->writeTo('path/to/directory');
```

В данном примере мы создали объект связанный с файловой системой по пути `/path/to/file.txt`, за тем прочитали его
содержимое, методом `setContent` добавили к содержимому в начало слово "change" и потом записали измененный
файл `file.txt`  в директорию `path/to/directory`. В результате создался файл `path/to/directory/file.txt` с измененным
содержимым.

Пример выполнения метода после связывания с файловой системой с изменением имени записываемого файла.

```php
$file = File::createBinded('/path/to/file.txt');
$file->read();
$file->setContent('change '. $file->getContent());
$file->setLocalName('fileRenamed.txt');
$file->writeTo('path/to/directory');
```

Мы сделали все то же самое, что и в предыдущем примере, только изменили его имя перед записью на "fileRenamed.txt".
Результат: `path/to/directory/fileRenamed.txt`.

> **Важное замечание!**<br>
> После выполнения метода `writeTo` связь с файловой системой не изменяется, источник остается прежним.

[К оглавлению](../../README.md#руководство)