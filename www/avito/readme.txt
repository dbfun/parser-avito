Требования к окружению

  PHP 5.2+
  MySQL 5.0

CLI-версия запускается из консоли.

  Windows: WIN+R, ввести cmd, запустить
  Linux: Ctrl+Alt+T открывает консоль

По причине долгой работы скрипта GUI-версия (запускается из браузера) не запрограммирована.

Порядок работы

1. Распаковать архив в любой каталог, например ~/avito/

2. Настройка
  2.1. Отредактировать файл avito/config.json (секция mysql) для подключения к базе данных
  2.2. Выполнить скрипт установки
    php avito/install/index.php
  
3. Парсинг объявлений
  Запустить грабер ссылок: php avito/parser.php 'название' 'ссылка' количество-страниц
    название - строка, служит для разделения вариантов запуска
    ссылка - страница, откуда будут браться ссылки на объявления
    Примеры:
    php avito/parser.php 'вся россия' 'http://www.avito.ru/rossiya' 10
    php avito/parser.php 'авто Екатеринбург' 'http://www.avito.ru/ekaterinburg/avtomobili_s_probegom' 5
    php avito/parser.php '2 комнатные квартиры' 'http://www.avito.ru/rossiya/kvartiry/prodam/2-komnatnye?i=1' 5

