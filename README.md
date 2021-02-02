# README For Time Manager

## How do I get set up?

```
git clone https://NagaiMatsuge@bitbucket.org/NagaiMatsuge/backend.git
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
```

## We provide data for testing purposes

```
php artisan db:seed
```

---

## Configuring AT for notifications(Go to App\Helpers\At.php for more information)

```
sudo apt-get install at
sudo systemctl enable --now atd (IF NOT ENABLED)
sudo chmod +x ./scripts/at_helper
sudo chown root:root ./scripts/at_helper
sudo ln ./scripts/at_helper /usr/local/bin
```

---

## Configure scripts for other purposes

- Make all files in scripts folder executable
- Link all files in scripts folder to /usr/local/bin
- Example: sudo ln ./scripts/virtualhost /usr/local/bin
- Open /etc/sudoers file and paste the following:

```
www-data ALL=(root) NOPASSWD: /usr/local/bin/create_sftp_user
www-data ALL=(root) NOPASSWD: /usr/local/bin/virtualhost
www-data ALL=(root) NOPASSWD: /usr/local/bin/update_user_password
www-data ALL=(ALL)  NOPASSWD: /usr/local/bin/at_helper
```

- Open cron and configure the following command line script to run every minute

```
php /path/to/project/artisan update:timer
```

---

## Websockets for Task-Chats

- We use [Laravel-websockets](https://beyondco.de/docs/laravel-websockets/getting-started/introduction) for websockets

```
composer require beyondcode/laravel-websockets

php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"

php artisan migrate

php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"

composer require pusher/pusher-php-server "~3.0"
```

- .env file update BROADCAST_DRIVER=pusher
- Edit config/broadcasting.php

```
  'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'encrypted' => true,
        'host' => '127.0.0.1',
        'port' => 6001,
        'scheme' => 'http'
    ],
],
```

- edit config/websockets.php

```
'apps' => [
    [
        'id' => env('PUSHER_APP_ID'),
        'name' => env('APP_NAME'),
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'enable_client_messages' => false,
        'enable_statistics' => true,
    ],
],
```

- Usage with Laravel echo

```
import Echo from "laravel-echo"

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'your-pusher-key',
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
});
```

---

## Configure supervisor for websockets

```
apt-get install supervisor
service supervisor restart
```

- configure location of the artisan file in time_manager_queue.conf and time_manager_websockets.conf

```
ln ./time_manager_websockets.conf /etc/supervisor/conf.d/
supervisorctl reread
supervisorctl update
```

- please check if above command in supervisor is active and running with following commands

```
sudo supervisorctl
status
```

---
