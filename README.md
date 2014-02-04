# Fusion Framework admin module

Lays out the base for your game's admin.

This module provides an admin generator to cut down on the time spent creating admin pages.

## Tasks

**admin:permissions**
Generates permissions based on classes extending the ```Admin``` base class.

```php minion admin:permissions```

**admin:routes**
Generates routes based on classes extending the ```Admin``` base class.

```php minion admin:routes```

## Cronjobs

**admin:stats**
Calculates and caches stats, which are displayed in the admin dashboard.

These stats gets cached for 24 hours, but I would advise you to run this cron every 5-30 minutes.

```php {PATH TO GAME FOLDER}minion admin:stats```