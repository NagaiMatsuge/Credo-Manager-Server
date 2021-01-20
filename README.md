# README By NagaiMatsuge

## Time Manager App

### How do I get set up?

- git clone https://NagaiMatsuge@bitbucket.org/NagaiMatsuge/backend.git
- cp .env.example .env
- composer install
- php artisan key:generate
- php artisan migrate

### We provide data for testing purposes

- php artisan db:seed

---

### Configuring AT for notifications(Go to App\Helpers\At.php for more information)

- sudo apt-get install at
- sudo systemctl enable --now atd (IF NOT ENABLED)

---

### Websockets for Task-Chats

- We use [Laravel-websockets](https://beyondco.de/docs/laravel-websockets/getting-started/introduction) for websockets
- composer require beyondcode/laravel-websockets
- php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
- php artisan migrate
- php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
- composer require pusher/pusher-php-server "~3.0"
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

### Configure supervisor for websockets

- apt-get install supervisor
- service supervisor restart
- configure location of the artisan file in time_manager_queue.conf and time_manager_websockets.conf
- cp ./time_manager_queue.conf /etc/supervisor/conf.d/
- cp ./time_manager_websockets.conf /etc/supervisor/conf.d/
- supervisorctl reread
- supervisorctl update
- please check if above two commands in supervisor are active and running with following commands

```
- sudo supervisorctl
- status

```
