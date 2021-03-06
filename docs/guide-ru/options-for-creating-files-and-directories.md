# Варианты создания файлов и директорий

У файлов и директорий есть два состояния. 

- Первое состояние - это когда объект еще не существует в физической файловой системе.
- Второе состояние - это когда объект связан с файловой системой, другими словами он существует.

Создавать объекты можно в обоих состояниях. Создаются файлы и директории одинаково. 

Пример:
```php 
// Файл
$file = new File('myFileName.txt');
// Директория
$directory = new Directory('myDirectoryName');
```
Так создается файл и директория которые еще не существуют в файловой системе. Важно то, что в конструктор
нельзя передать путь к объекту файловой системы, ожидается именно имя файла для файлов и имя директории для директорий.
Если вы попытаетесь передать путь, это вызовет исключение `LogicException`. Это же исключение 
вызовет и переданная пустая строка. Для правильной работы, система ожидает имя.

Второе состояние требует создания через статический метод `createBinded(string $path)`.
```php 
// Файл
$file = File::createBinded('/var/www/myFileName.txt');
// Директория
$directory = Directory::createBinded('/var/www');
```

По умолчанию файлы и директории использует класс `\Vinograd\SimpleFiles\DefaultFilesystem` 
для работы с физической файловой системой, и эта файловая система является локальной.
Если файла или директории по переданному пути не существует, будет выброшено исключение 
`\Vinograd\IO\Exception\NotFoundException`. Можно передавать методу создания как абсолютные пути, 
так и относительные. Правило простое - `DefaultFilesystem` внутри 
себя использует php функцию `realpath(...)`. Все возвраты со значением `false` этой функцией, 
вызовут исключение (см. документацию `realpath(...)` в каких случаях эта функция может 
вернуть `false`).
Проверить связан ли объект с файловой системой можно с помощью метода `public function isBinded(): bool`.

### Как, и в каких случаях нужно связывать файлы или директории с файловой системой?

Связать новый объект с файловой системой можно с помощью метода 
`public function bindWithFilesystem(string $path): void`. Связывать новые объекты с файловой 
системой может понадобиться в случае, если вы выбираете где связывать по какому, либо условию.
Связывание директории с файловой системой и связывание файла несколько отличаются друг от друга.
В основном это отличие связано с тем, что файл имеет данные которые он хранит.

#### Связывание директорий
```php 
$directory = new Directory('root');
$directory->bindWithFilesystem('/var/www');
```
В примере выше, директория будет создана в случае ее отсутствия, в каталоге `/var/www` 
и будет иметь путь источника `/var/www/root`. Если директория существует, произойдет ровно 
тоже самое кроме создания директории.

#### Связывание файлов
```php 
$file = new File('file.txt');
$file->bindWithFilesystem('/var/www');
```
В примере выше файл будет создан в случае его отсутствия в каталоге `/var/www` и будет иметь 
путь источника `/var/www/file.txt`. При этом будет создан пустой файл. Если файл существует 
по этому пути, то новый файл просто свяжется с ним, не перезаписывая его.
Основное здесь то, что запись данных в файл не производится при связывании с файловой системой.
Это сделано для того, чтобы можно было прочитать данные из связанного источника.

> Путь с которым вы связываете должен существовать, в данном случае это `/var/www`. И еще одно 
> важное замечание. Вы не пишете имя файла или директории в конце пути. 
> - Пример как **не правильно** для директорий: `$directory->bindWithFilesystem('/var/www/root');`
> - Пример как **не правильно** для файлов: `$file->bindWithFilesystem('/var/www/file.txt');`

Связывать или не связывать новые объекты зависит от задачи, это не обязательная операция.
Например, копировать и перемещать можно и не связанные с файловой системой файлы и 
директории. Об этом читайте в соответствующих разделах руководства.

[К оглавлению](README.md)