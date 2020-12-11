<?php

return [
    'urls' => [
        'front_end_password_reset' => 'http=>//localhost=>8080/password/reset'
    ],
    'response_messages' => [
        'auth/too-many-requests' => 'Превышен лимит на запрос смены пароля',
        'auth/email-already-in-use' => 'Пользователь с таким Email уже зарегистрирован',
        'auth/user-not-found' => 'Пользователь с таким Email не зарегистрирован',
        'auth/wrong-password' => 'Введите правельный пороль',
        'email-verify/email_of_the_user@gmail.com' => 'Емейл активации аккаунта был отправлен на почту email_of_the_user@gmail.com',
        'email-reset/email_of_the_user@gmail.com' => 'Емейл сброса пароля был отправлен на почту email_of_the_user@gmail.com'
    ]
];
