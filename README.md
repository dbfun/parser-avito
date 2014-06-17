Порядок работы
1. Установка
  При его выполнении настраивается подключение к MySQL
  CLI:
    php install/index.php
  GUI:
    открыть install/index.html
  Вручную:
    отредактировать файл config.json (секция mysql), выполнить SQL-команду из файла "install/install.sql"

2. Поиск ссылок
  CLI: 
    Запуск грабера ссылок: php parser.php 'название' 'ссылка' количество-страниц
    номер - строка, служит для разделения вариантов запуска
    ссылка - страница, откуда будут браться ссылки на объявления
    Примеры:
    php parser.php 'вся россия' 'http://www.avito.ru/rossiya'
    php parser.php 'авто Екатеринбург' 'http://www.avito.ru/ekaterinburg/avtomobili_s_probegom'
    php parser.php '2 комнатные квартиры' 'http://www.avito.ru/rossiya/kvartiry/prodam/2-komnatnye?i=1'
  GUI: 
    открыть parser.html
