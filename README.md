# Simple-files

> Simple-files - это универсальная библиотека для работы с файлами и директориями, которая упрощает сложные манипуляции
> с файлами. Также она имеет API для построения работы с различными форматами файлов. Библиотека разделяет логику
> обработки данных и работу с файловой системой. Это означает, что вы можете создать приложение, которое работает с
> файлами, не зная при этом, с какой файловой системой оно работает.

## Установка

- Минимальная версия php 8.0
- Предпочтительный способ установки - через [composer](http://getcomposer.org/download/).

> Windows не поддерживается.

Запустите команду

```
composer require vinogradsoft/simple-files "^2.0"
```

## Руководство

### Общая работа с библиотекой

* [Варианты создания объектов файлов и директорий](./docs/guide-ru/options-for-creating-file-and-directory-objects.md)

### Работа с файлами

* [Чтение файлов](./docs/guide-ru/reading-files.md)
* [Запись файлов](./docs/guide-ru/recording-files.md)
* [Копирование файлов](./docs/guide-ru/copying-files.md)
* [Перемещение файлов](./docs/guide-ru/moving-files.md)
* [Удаление файлов](./docs/guide-ru/deleting-files.md)
* [Обработка содержимого файла перед записью](./docs/guide-ru/processing-file-contents-before-writing.md)

### Работа с директориями

* [Объектная модель файловой системы](./docs/guide-ru/file-system-object-model.md)
* [Структурные методы директорий](./docs/guide-ru/directory-structural-methods.md)
* [Прикладные методы директорий](./docs/guide-ru/applied-methods.md)
* [Копирование директорий](./docs/guide-ru/copying-directories.md)
* [Запись директорий](./docs/guide-ru/writing-directories.md)
* [Перемещение директорий](./docs/guide-ru/stub.md)
* [Удаление директорий](./docs/guide-ru/stub.md)

### Работа с функциональностями

* [Контекст файловой системы](./docs/guide-ru/stub.md)
* [Файловая система](./docs/guide-ru/stub.md)
* [Создание новой функциональности](./docs/guide-ru/stub.md)
* [Области действия функциональностей](./docs/guide-ru/stub.md)
* [Создание слушателя изменения полей объекта](./docs/guide-ru/stub.md)

## Тестировать

```
 php composer tests 
```

## Содействие

Пожалуйста, смотрите [ВКЛАД](CONTRIBUTING.md) для получения подробной информации.

## Лицензия

Лицензия MIT (MIT). Пожалуйста, смотрите [файл лицензии](LICENSE) для получения дополнительной информации.