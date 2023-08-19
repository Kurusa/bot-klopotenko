<?php

return [
    'complexity_data' => [
        'easy' => [
            'emoji' => '🟢',
            'title' => 'легка',
        ],
        'medium' => [
            'emoji' => '🟠',
            'title' => 'середня',
        ],
        'hard' => [
            'emoji' => '🔴',
            'title' => 'складна',
        ],
    ],

    'complexity_map' => [
        'легко'   => 'easy',
        'помірно' => 'medium',
        'складно' => 'hard',
    ],

    'ratings' => [
        '1' => '☹️',
        '2' => '😕',
        '3' => '😐',
        '4' => '🙂',
        '5' => '😊',
    ],

    'notification_types' => [
        // -3 hours
        'morning' => [
            'title' => 'сніданок 7:00',
            'time'  => '1:00-8:00',
        ],
        'dinner'  => [
            'title' => 'обід 14:00',
            'time'  => '8:00-13:00',
        ],
        'evening' => [
            'title' => 'вечеря 19:00',
            'time'  => '13:00-1:00',
        ],
    ],
];
