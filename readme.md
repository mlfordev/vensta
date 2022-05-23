```text
сделать на php/symfony
сайт 2 страницы

БД mysql 1 таблица - объявления. поля имя, текст

1 - основная список объявлений (со ссылкой на просмотр объявления) с пагинацией по 5 объявлений на страницу
основная страница uri: /
пагинация uri: /{page}

2 - показ объявления: имя/текст + картинка (может быть только одна или может не быть).
uri /view/{some_id}

страницы генерируются twig
генерация таблиц бд средствами symfony/doctrine
доступ к БД на 1ой странице doctrine dbal, на 2ой doctrine orm
работу с БД вынести из контроллера в сервис/репозиторий
```

- Версия PHP `8.1`
- Версия MySQL `5.7`

### Разворачивание проекта

- `git clone git@github.com:mlfordev/vensta.git <same dir>`
- `cd <same dir>`
- переименовываем файл `.env.example` в `.env`
- в файле `.env` заполняем `APP_SECRET`
- `composer install`
- `npm install`
- `npm run build`
- вписываем настройки БД в файл `.env`
- `php bin/console doctrine:database:create`
- `php bin/console make:migration`
- `php bin/console doctrine:migrations:migrate`
- настраиваем `DOCUMENT_ROOT` сервера на папку `public`




