# API Importer

## Описание

Проект на Laravel 8 (PHP 8.1, Octane) для импорта данных из внешнего API (продажи, заказы, склады, доходы) с сохранением в MySQL. Импорт поддерживает пагинацию, авторизацию по токену, валидацию и логирование ошибок.

## Стек
- Docker, docker-compose
- PHP 8.1
- Laravel 8
- Laravel Octane
- MySQL 8
- phpMyAdmin

## Быстрый старт

### 1. Клонирование и запуск
```bash
git clone https://github.com/zobnyyn/testAPI
cd api-importer
docker-compose up --build -d
```

### 2. Настройка .env
В папке src скопируйте .env.example в .env и укажите параметры подключения к БД и API:

```
DB_HOST=db
DB_PORT=3306
DB_DATABASE=api_importer
DB_USERNAME=root
DB_PASSWORD=secret
API_BASE_URL=http://109.73.206.144:6969
API_KEY=E6kUTYrYwZq2tN4QEtyzsbEBk3ie
```

### 3. Миграции
```bash
docker compose exec app php artisan migrate:fresh --force
```

### 4. Импорт данных
```bash
docker compose exec app php artisan import:all --dateFrom=2025-07-01 --dateTo=2025-07-24
```

- Все эндпоинты API возвращают ответ в формате JSON с пагинацией (используются параметры page и limit).
- Для stocks всегда берётся текущая дата.
- Все ошибки и пропуски логируются в storage/logs/import_errors.log.

### 5. Доступ к базе через phpMyAdmin
- Откройте http://localhost:8080
- Сервер: db
- Пользователь: root
- Пароль: secret

## Структура таблиц
- sales
- orders
- stocks
- incomes

(См. миграции в src/database/migrations)

## Логирование ошибок
Все ошибки импорта и пропущенные записи пишутся в storage/logs/import_errors.log.

## Развёртывание БД на бесплатном хостинге
- Можно использовать db4free.net, freemysqlhosting.net и т.п.
- После развёртывания укажите доступы и названия таблиц.

## Контакты
Если возникнут вопросы — пишите!
