# API Importer

## Описание

Проект на Laravel 8 (PHP 8.1, Octane) для импорта данных из внешнего API (продажи, заказы, склады, доходы) с сохранением в MySQL. Импорт поддерживает пагинацию, авторизацию по токену, валидацию и логирование ошибок.

## Стек
- Docker, docker-compose
- PHP 8.1
- Laravel 8
- Laravel Octane
- MySQL 8

## Быстрый старт

### 1. Клонирование и запуск
```bash
git clone https://github.com/zobnyyn/testAPI
cd api-importer
docker-compose up --build -d
```

### 2. Настройка 

```
DB_HOST=zobnyynmysql-ordelicsgo-ab3f.c.aivencloud.com
DB_PORT=22768
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=AVNS_fX3mIqqXD-b7D1ku_MI

```
- Все эндпоинты API возвращают ответ в формате JSON с пагинацией (используются параметры page и limit).
- Для stocks всегда берётся текущая дата.
- Все ошибки и пропуски логируются в storage/logs/import_errors.log.

## Структура таблиц
- sales
- orders
- stocks
- incomes

## Логирование ошибок
Все ошибки импорта и пропущенные записи пишутся в storage/logs/import_errors.log.

