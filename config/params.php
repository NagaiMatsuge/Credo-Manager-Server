<?php

return [
    'urls' => [
        'front_end_password_reset' => 'http://localhost:8080/password/reset',
        'front_base_url' => 'http://localhost:8080'
    ],
    'roles' => [
        'Admin',
        'Manager',
        'Backend Developer',
        'Frontend Developer',
        'UI\UX Designer',
    ],
    'month_format' => [
        '01' => 'январь',
        '02' => 'февраль',
        '03' => 'март',
        '04' => 'апрель',
        '05' => 'май',
        '06' => 'июнь',
        '07' => 'июль',
        '08' => 'август',
        '09' => 'сентябрь',
        '10' => 'октябрь',
        '11' => 'ноябрь',
        '12' => 'декабрь',
    ],
    'colors' => [
        '#8F73FC',
        '#FC73AD',
        '#FCB573'
    ],
    'currencies' => [
        '1' => 'SUM',
        '2' => 'USD',
        '3' => 'RUB'
    ],
    'payment_types' => [
        '1' => 'Qiwi',
        '2' => 'Yandex',
        '3' => 'Перечисление',
        '4' => 'Денежные переводы',
        '5' => 'Другое'
    ],
    'payment_image_urls' => [
        '1' => 'Qiwi.svg',
        '2' => 'YandexMoney.svg',
        '3' => 'perechisleniye.svg',
        '4' => 'money-transfer.svg',
        '5' => ''
    ],
    'response_messages' => [
        'auth/too-many-requests' => 'Превышен лимит на запрос смены пароля',
        'auth/email-already-in-use' => 'Пользователь с таким Email уже зарегистрирован',
        'auth/user-not-found' => 'Пользователь с таким Email не зарегистрирован',
        'auth/wrong-password' => 'Введите правельный пороль',

        'email-verify/email_of_the_user@gmail.com' => 'Емейл активации аккаунта был отправлен на почту email_of_the_user@gmail.com',

        'email-reset/email_of_the_user@gmail.com' => 'Емейл сброса пароля был отправлен на почту email_of_the_user@gmail.com',
        'auth/email-verified' => "Email был успешно активирован",
        'not-found/user' => 'User не найден',
        'repeat/email-verified' => 'Email уже активирован',
        'link/expire' => 'Линк испорился',
        'auth/fail' => 'Неправилные данные',
        'auth/email-not-verified' => 'Email не активирован'
    ]
];
