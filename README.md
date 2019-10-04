# Display sub pages of the current page, drag & drop to change orders(sort_value)

-   This is an Amila Laravel CMS Plugin
-   Display sub pages of the current page, drag & drop to change orders(sort_value)

## Install it via the backend

-   Go to the CMS settings page -> Plugin -> search for remote image
-   Find alexstack/laravel-cms-plugin-sub-page
-   Click the Install button

## What the plugin do for us?

-   Display sub pages of the current page
-   Drag & drop to change orders(sort_value)

## Install it via command line manually

```php
composer require alexstack/laravel-cms-plugin-sub-page

php artisan migrate --path=./vendor/alexstack/laravel-cms-plugin-sub-page/src/database/migrations

php artisan vendor:publish --force --tag=sub-page-views

php artisan laravelcms --action=clear

```

## How to use it?

-   It's enabled after install by default. You can see a Sub Page tab when you edit a page.
-   You don't need to do anything after install

## How to change the settings?

-   You can change the settings by edit plugin.page-tab-sub-page

```json

```

## Improve this plugin & documents

-   You are very welcome to improve this plugin and how to use documents

## License

-   This Amila Laravel CMS plugin is an open-source software licensed under the MIT license.
