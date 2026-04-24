# Laravel + Vue Feedback SPA

Тестовое задание: SPA-форма обратной связи на Laravel и Vue.

## Что реализовано

- SPA на `Vue 3` + `Vuex 4` + `Vue Router 4`
- Две страницы:
  - `/new` — форма (имя, обращение), отправка на backend и сохранение во Vuex
  - `/list` — список отправленных заявок только из Vuex (без запроса списка к backend)
- Backend API `POST /api/feedbacks` на Laravel
- Паттерн Factory для выбора канала сохранения: `database` или `email`
- Адаптация под shared hosting без смены `document root` (корневые `.htaccess` и `index.php`)

## Стек

- PHP `8.4+` (на проде целевой `8.5`)
- Laravel `13`
- Vue `3`, Vuex `4`, Vue Router `4`
- Vite, Axios
- PHPUnit

## Архитектура backend

Доменный слой вынесен в `app/Services/Feedback`:

- `Contracts/FeedbackSaverInterface.php` — общий контракт сохранения
- `Drivers/DatabaseFeedbackSaver.php` — сохранение в БД через Eloquent
- `Drivers/EmailFeedbackSaver.php` — сохранение через отправку email
- `FeedbackSaverFactory.php` — фабрика, принимающая драйвер (`database`/`email`) и предоставляющая `save()`
- `Data/FeedbackData.php` — immutable DTO
- `Exceptions/UnsupportedFeedbackDriverException.php`

HTTP-слой:

- `app/Http/Requests/StoreFeedbackRequest.php` — валидация + конвертация в DTO
- `app/Http/Controllers/Api/FeedbackController.php` — приём данных, вызов фабрики, JSON-ответы `201/503`
- `routes/api.php` — `POST /api/feedbacks`

Конфигурирование:

- `config/feedback.php`
- `app/Providers/FeedbackServiceProvider.php`
- Переменные окружения: `FEEDBACK_DRIVER`, `FEEDBACK_EMAIL_RECIPIENT`, `FEEDBACK_EMAIL_SUBJECT`

## Архитектура frontend

- `resources/js/router/index.js` — маршруты `/new`, `/list`, `404`
- `resources/js/store/modules/feedback.js` — Vuex-модуль обратной связи
- `resources/js/api/http.js`, `resources/js/api/feedback.js` — API-клиент и обработка ошибок
- `resources/js/views/FormView.vue` — форма, inline-валидация, тосты
- `resources/js/views/ListView.vue` — список из Vuex (без fetch списка)
- `resources/js/views/NotFoundView.vue`

Интерфейс локализован на русский язык.

## SOLID / инженерные решения

- **SRP**: контроллер, фабрика, драйверы и DTO разделены по ответственности
- **OCP**: добавление нового драйвера делается без изменения контроллера и контракта
- **LSP**: драйверы взаимозаменяемы через `FeedbackSaverInterface`
- **ISP**: минимальный контракт `save(FeedbackData $data): bool`
- **DIP**: фабрика зависит от абстракции/контейнера, драйверы получают зависимости через DI

## Тестирование

Покрыты критичные сценарии:

- Unit:
  - `tests/Unit/Services/Feedback/FeedbackDataTest.php`
  - `tests/Unit/Services/Feedback/FeedbackSaverFactoryTest.php`
- Feature:
  - `tests/Feature/Api/FeedbackControllerTest.php`
  - `tests/Feature/Services/Feedback/DatabaseFeedbackSaverTest.php`
  - `tests/Feature/Services/Feedback/EmailFeedbackSaverTest.php`

## Локальный запуск

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

## Команды качества

```bash
php artisan test
npm run build
```

## Деплой на shared hosting (без смены document root)

1. Загрузить проект в корень сайта.
2. Заполнить `.env` реальными значениями.
3. Выполнить (через PHP 8.5 CLI):

```bash
/opt/php85/bin/php artisan migrate --force
/opt/php85/bin/php artisan optimize:clear
/opt/php85/bin/php artisan config:cache
/opt/php85/bin/php artisan route:cache
/opt/php85/bin/php artisan view:cache
```

4. Проверить права на запись:
   - `storage/`
   - `bootstrap/cache/`
